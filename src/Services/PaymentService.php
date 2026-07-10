<?php

namespace Laraditz\TngEwallet\Services;

use Laraditz\TngEwallet\Client\Contracts\ClientInterface;
use Laraditz\TngEwallet\Enums\PaymentStatus;
use Laraditz\TngEwallet\Enums\ResultStatus;
use Laraditz\TngEwallet\Models\Payment;
use Laraditz\TngEwallet\Responses\InquiryPaymentResponse;
use Laraditz\TngEwallet\Responses\PayResponse;

class PaymentService
{
    public function __construct(protected ClientInterface $client)
    {
    }

    public function pay(array $data): PayResponse
    {
        $response = new PayResponse($this->client->post('/v1/payments/pay', $data));

        Payment::create([
            'payment_id' => $response->paymentId,
            'payment_request_id' => $data['paymentRequestId'],
            'status' => $this->mapResultStatusToPaymentStatus($response->resultStatus)->value,
            'result_status' => $response->resultStatus,
            'result_code' => $response->resultCode,
            'currency' => $data['paymentAmount']['currency'] ?? null,
            'amount' => $data['paymentAmount']['value'] ?? null,
            'action_form_type' => $response->actionForm?->actionFormType,
            'redirection_url' => $response->actionForm?->redirectionUrl,
            'payment_time' => $response->paymentTime,
            'auth_expiry_time' => $response->authExpiryTime,
            'raw_pay_response' => $response->raw(),
        ]);

        return $response;
    }

    public function inquiry(array $data): InquiryPaymentResponse
    {
        return new InquiryPaymentResponse($this->client->post('/v1/payments/inquiryPayment', $data));
    }

    protected function mapResultStatusToPaymentStatus(?string $resultStatus): PaymentStatus
    {
        return match ($resultStatus) {
            ResultStatus::Accepted->value => PaymentStatus::Accepted,
            ResultStatus::Success->value => PaymentStatus::Success,
            ResultStatus::Failed->value => PaymentStatus::Failed,
            ResultStatus::Unknown->value => PaymentStatus::Unknown,
            default => PaymentStatus::Created,
        };
    }
}
