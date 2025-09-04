<?php
// app/Http/Controllers/Api/TemplatePurchaseController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Template;
use App\Models\TemplatePurchase;
use Stripe\Stripe;
use Stripe\Charge;

class TemplatePurchaseController extends Controller
{
    public function buyMultiple(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'template_ids' => 'required|array|min:1',
            'template_ids.*' => 'exists:templates,id',
            'stripe_token' => 'required|string',
            'currency'     => 'nullable|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $templates = Template::whereIn('id', $request->template_ids)
            ->where('status', 'active')
            ->get();

        if ($templates->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No valid templates found.'], 400);
        }

        // Calculate total
        $totalAmount = $templates->sum(function ($t) {
            return (float) ($t->price ?? 0);
        });

        if ($totalAmount <= 0) {
            return response()->json(['success' => false, 'message' => 'Selected templates are free, no payment required.'], 400);
        }

        $currency = $request->input('currency', 'usd');

        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            $charge = \Stripe\Charge::create([
                "amount"      => intval($totalAmount * 100),
                "currency"    => $currency,
                "source"      => $request->stripe_token,
                "description" => "Multiple template purchase by User ".Auth::id(),
            ]);

            $purchases = [];

            foreach ($templates as $template) {
                // Skip if already owned
                $exists = TemplatePurchase::where('user_id', Auth::id())
                    ->where('template_id', $template->id)
                    ->where('status', 'completed')
                    ->first();

                if ($exists) {
                    $purchases[] = $this->purchaseResource($exists);
                    continue;
                }

                $purchase = TemplatePurchase::create([
                    'user_id'       => Auth::id(),
                    'template_id'   => $template->id,
                    'category_id'   => $template->category_id,
                    'amount'        => (float)($template->price ?? 0),
                    'currency'      => $currency,
                    'status'        => 'completed',
                    'payment_method'=> 'stripe',
                    'transaction_id'=> $charge->id,
                    'meta'          => $charge->toArray(),
                ]);

                $purchases[] = $this->purchaseResource($purchase);
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment successful. Templates unlocked!',
                'data'    => [
                    'total_amount' => $totalAmount,
                    'currency'     => $currency,
                    'transaction_id' => $charge->id,
                    'templates'    => $purchases,
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment failed: '.$e->getMessage(),
            ], 500);
        }
    }


    // /**
    //  * Payment callback/webhook to mark purchase completed
    //  * POST /api/purchases/{purchase}/confirm
    //  */
    // public function confirm(Request $request, TemplatePurchase $purchase)
    // {
    //     $request->validate([
    //         'status'         => 'required|string|in:completed,failed',
    //         'transaction_id' => 'nullable|string',
    //         'meta'           => 'nullable|array',
    //     ]);

    //     // authorize: only owner OR webhook key (add your auth as needed)
    //     if ($purchase->user_id !== Auth::id()) {
    //         return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
    //     }

    //     $purchase->status = $request->status;
    //     if ($request->filled('transaction_id')) {
    //         $purchase->transaction_id = $request->transaction_id;
    //     }
    //     if ($request->filled('meta')) {
    //         $purchase->meta = $request->meta;
    //     }
    //     $purchase->save();

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Purchase status updated.',
    //         'data'    => $this->purchaseResource($purchase)
    //     ]);
    // }

    // /**
    //  * GET /api/my/templates  → owned templates list
    //  */
    // public function myTemplates(Request $request)
    // {
    //     $purchases = TemplatePurchase::with(['template'])
    //         ->where('user_id', Auth::id())
    //         ->where('status', 'completed')
    //         ->latest()
    //         ->get();

    //     $data = $purchases->map(fn($p) => [
    //         'purchase_id'   => $p->id,
    //         'template_id'   => $p->template_id,
    //         'category_id'   => $p->category_id,
    //         'amount'        => (float)$p->amount,
    //         'currency'      => $p->currency,
    //         'purchased_at'  => $p->created_at?->toIso8601String(),
    //         'template'      => [
    //             'heading'     => $p->template->heading ?? null,
    //             'type'        => $p->template->type ?? null,
    //             'key'         => $p->template->key ?? null,
    //             'image'       => $p->template->image ?? null,
    //             'slug'        => $p->template->slug ?? null,
    //         ],
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Owned templates.',
    //         'data'    => $data,
    //     ]);
    // }

    private function purchaseResource(TemplatePurchase $p): array
    {
        return [
            'purchase_id'   => $p->id,
            'user_id'       => $p->user_id,
            'template_id'   => $p->template_id,
            'category_id'   => $p->category_id,
            'amount'        => (float)$p->amount,
            'currency'      => $p->currency,
            'status'        => $p->status,
            'payment_method'=> $p->payment_method,
            'transaction_id'=> $p->transaction_id,
            'meta'          => $p->meta,
            'created_at'    => optional($p->created_at)->toIso8601String(),
        ];
    }
}
