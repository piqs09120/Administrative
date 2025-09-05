<?php

namespace App\Http\Controllers;

use App\Models\FacilityReservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'reservation_id' => 'required|exists:facility_reservations,id',
            'payment_method' => 'required|in:credit_card,debit_card,paypal,bank_transfer',
            'amount' => 'required|numeric|min:0.01'
        ]);

        $reservation = FacilityReservation::with('facility')->findOrFail($request->reservation_id);
        
        // Verify the reservation belongs to the authenticated user
        if ($reservation->reserved_by !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to reservation.'
            ], 403);
        }

        // Calculate the total amount based on facility hourly rate and duration
        $startTime = \Carbon\Carbon::parse($reservation->start_time);
        $endTime = \Carbon\Carbon::parse($reservation->end_time);
        $duration = $startTime->diffInHours($endTime);
        $calculatedAmount = $reservation->facility->hourly_rate * $duration;

        // Verify the payment amount matches the calculated amount
        if (abs($request->amount - $calculatedAmount) > 0.01) {
            return response()->json([
                'success' => false,
                'message' => 'Payment amount does not match the calculated reservation cost.'
            ], 400);
        }

        try {
            // Process payment (this would integrate with actual payment gateway)
            $paymentResult = $this->processPaymentGateway($request, $reservation);

            if ($paymentResult['success']) {
                // Update reservation with payment information
                $reservation->update([
                    'payment_status' => 'paid',
                    'payment_method' => $request->payment_method,
                    'payment_amount' => $request->amount,
                    'payment_transaction_id' => $paymentResult['transaction_id'],
                    'payment_processed_at' => now()
                ]);

                // Log the payment
                Log::info('Payment processed successfully', [
                    'reservation_id' => $reservation->id,
                    'user_id' => Auth::id(),
                    'amount' => $request->amount,
                    'transaction_id' => $paymentResult['transaction_id']
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Payment processed successfully!',
                    'transaction_id' => $paymentResult['transaction_id'],
                    'reservation_id' => $reservation->id
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $paymentResult['message']
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Payment processing failed', [
                'reservation_id' => $reservation->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed. Please try again.'
            ], 500);
        }
    }

    public function getPaymentDetails($reservationId)
    {
        $reservation = FacilityReservation::with('facility')->findOrFail($reservationId);
        
        // Verify the reservation belongs to the authenticated user
        if ($reservation->reserved_by !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to reservation.'
            ], 403);
        }

        // Calculate payment details
        $startTime = \Carbon\Carbon::parse($reservation->start_time);
        $endTime = \Carbon\Carbon::parse($reservation->end_time);
        $duration = $startTime->diffInHours($endTime);
        $hourlyRate = $reservation->facility->hourly_rate ?? 0;
        $subtotal = $hourlyRate * $duration;
        $tax = $subtotal * 0.1; // 10% tax
        $total = $subtotal + $tax;

        return response()->json([
            'success' => true,
            'payment_details' => [
                'facility_name' => $reservation->facility->name,
                'reservation_date' => $reservation->start_time->format('M d, Y'),
                'reservation_time' => $reservation->start_time->format('g:i A') . ' - ' . $reservation->end_time->format('g:i A'),
                'duration_hours' => $duration,
                'hourly_rate' => $hourlyRate,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'payment_status' => $reservation->payment_status ?? 'pending'
            ]
        ]);
    }

    public function paymentHistory()
    {
        $payments = FacilityReservation::where('reserved_by', Auth::id())
            ->whereNotNull('payment_transaction_id')
            ->with('facility')
            ->orderBy('payment_processed_at', 'desc')
            ->paginate(10);

        return view('payments.history', compact('payments'));
    }

    private function processPaymentGateway($request, $reservation)
    {
        // This is a mock payment gateway integration
        // In a real application, you would integrate with Stripe, PayPal, etc.
        
        // Simulate payment processing
        $transactionId = 'TXN_' . time() . '_' . rand(1000, 9999);
        
        // Simulate different payment method processing
        switch ($request->payment_method) {
            case 'credit_card':
            case 'debit_card':
                // Simulate card processing
                if (rand(1, 10) > 1) { // 90% success rate
                    return [
                        'success' => true,
                        'transaction_id' => $transactionId,
                        'message' => 'Card payment processed successfully'
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Card payment declined. Please check your card details.'
                    ];
                }
                
            case 'paypal':
                // Simulate PayPal processing
                if (rand(1, 10) > 2) { // 80% success rate
                    return [
                        'success' => true,
                        'transaction_id' => $transactionId,
                        'message' => 'PayPal payment processed successfully'
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'PayPal payment failed. Please try again.'
                    ];
                }
                
            case 'bank_transfer':
                // Simulate bank transfer
                return [
                    'success' => true,
                    'transaction_id' => $transactionId,
                    'message' => 'Bank transfer initiated. Payment will be processed within 1-2 business days.'
                ];
                
            default:
                return [
                    'success' => false,
                    'message' => 'Invalid payment method.'
                ];
        }
    }
}
