<?php

namespace Tests\YandexCheckout\Request\Payments;

use PHPUnit\Framework\TestCase;
use YandexCheckout\Helpers\Random;
use YandexCheckout\Model\CancellationDetailsPartyCode;
use YandexCheckout\Model\CancellationDetailsReasonCode;
use YandexCheckout\Model\ConfirmationType;
use YandexCheckout\Model\CurrencyCode;
use YandexCheckout\Model\PaymentInterface;
use YandexCheckout\Model\PaymentMethodType;
use YandexCheckout\Model\PaymentStatus;
use YandexCheckout\Model\ReceiptRegistrationStatus;
use YandexCheckout\Request\Payments\PaymentsResponse;

class PaymentsResponseTest extends TestCase
{
    /**
     * @dataProvider validDataProvider
     * @param array $options
     */
    public function testGetItems($options)
    {
        $instance = new PaymentsResponse($options);
        self::assertEquals(count($options['items']), count($instance->getItems()));
        foreach ($instance->getItems() as $index => $item) {
            self::assertTrue($item instanceof PaymentInterface);
            self::assertArrayHasKey($index, $options['items']);
            self::assertEquals($options['items'][$index]['id'], $item->getId());
            self::assertEquals($options['items'][$index]['status'], $item->getStatus());
            self::assertEquals($options['items'][$index]['amount']['value'], $item->getAmount()->getValue());
            self::assertEquals($options['items'][$index]['amount']['currency'], $item->getAmount()->getCurrency());
            self::assertEquals($options['items'][$index]['created_at'], $item->getCreatedAt()->format(DATE_ATOM));
            self::assertEquals($options['items'][$index]['payment_method']['type'], $item->getPaymentMethod()->getType());
            self::assertEquals($options['items'][$index]['paid'], $item->getPaid());
            self::assertEquals($options['items'][$index]['refundable'], $item->getRefundable());
        }
    }

    /**
     * @dataProvider validDataProvider
     * @param array $options
     */
    public function testGetNextCursor($options)
    {
        $instance = new PaymentsResponse($options);
        if (empty($options['next_cursor'])) {
            self::assertNull($instance->getNextCursor());
        } else {
            self::assertEquals($options['next_cursor'], $instance->getNextCursor());
        }
    }

    /**
     * @dataProvider validDataProvider
     * @param array $options
     */
    public function testHasNext($options)
    {
        $instance = new PaymentsResponse($options);
        if (empty($options['next_cursor'])) {
            self::assertFalse($instance->hasNextCursor());
        } else {
            self::assertTrue($instance->hasNextCursor());
        }
    }

    public function validDataProvider()
    {
        return array(
            array(
                array(
                    'items' => array(),
                ),
            ),
            array(
                array(
                    'items' => array(
                        array(
                            'id' => Random::str(36),
                            'status' => PaymentStatus::SUCCEEDED,
                            'amount' => array(
                                'value' => Random::int(1, 100000),
                                'currency' => CurrencyCode::EUR,
                            ),
                            'description' => Random::str(20),
                            'created_at' => date(DATE_ATOM),
                            'payment_method' => array(
                                'type' => PaymentMethodType::QIWI,
                            ),
                            'paid' => false,
                            'refundable' => false,
                        )
                    ),
                    'next_cursor' => uniqid(),
                ),
            ),
            array(
                array(
                    'items' => array(
                        array(
                            'id' => Random::str(36),
                            'status' => PaymentStatus::SUCCEEDED,
                            'amount' => array(
                                'value' => Random::int(1, 100000),
                                'currency' => CurrencyCode::EUR,
                            ),
                            'created_at' => date(DATE_ATOM),
                            'payment_method' => array(
                                'type' => PaymentMethodType::QIWI,
                            ),
                            'paid' => true,
                            'refundable' => true,
                            'confirmation' => array(
                                'type' => ConfirmationType::EXTERNAL,
                            ),
                        ),
                        array(
                            'id' => Random::str(36),
                            'status' => PaymentStatus::SUCCEEDED,
                            'amount' => array(
                                'value' => Random::int(1, 100000),
                                'currency' => CurrencyCode::EUR,
                            ),
                            'description' => Random::str(20),
                            'created_at' => date(DATE_ATOM),
                            'payment_method' => array(
                                'type' => PaymentMethodType::QIWI,
                            ),
                            'paid' => false,
                            'refundable' => false,
                            'recipient' => array(
                                'account_id' => uniqid(),
                                'gateway_id' => uniqid(),
                            ),
                            'reference_id' => uniqid(),
                            'captured_at' => date(DATE_ATOM),
                            'charge' => array('value' => Random::int(1, 100000), 'currency' => CurrencyCode::RUB),
                            'income' => array('value' => Random::int(1, 100000), 'currency' => CurrencyCode::USD),
                            'refunded' => array('value' => Random::int(1, 100000), 'currency' => CurrencyCode::EUR),
                            'metadata' => array('test_key' => 'test_value'),
                            'cancellation_details' => array('party' => CancellationDetailsPartyCode::PAYMENT_NETWORK, 'reason' => CancellationDetailsReasonCode::INVALID_CSC),
                            'authorization_details' => array('rrn' => Random::str(20), 'auth_code' => Random::str(20)),
                            'refunded_amount' => array('value' => Random::int(1, 100000), 'currency' => CurrencyCode::RUB),
                            'confirmation' => array(
                                'type' => ConfirmationType::EXTERNAL,
                            ),
                            'receipt_registration' => ReceiptRegistrationStatus::PENDING,
                        ),
                    ),
                    'next_cursor' => uniqid(),
                ),
            ),
            array(
                array(
                    'items' => array(
                        array(
                            'id' => Random::str(36),
                            'status' => PaymentStatus::SUCCEEDED,
                            'amount' => array(
                                'value' => Random::int(1, 100000),
                                'currency' => CurrencyCode::EUR,
                            ),
                            'description' => Random::str(20),
                            'created_at' => date(DATE_ATOM),
                            'payment_method' => array(
                                'type' => PaymentMethodType::QIWI,
                            ),
                            'paid' => true,
                            'refundable' => true,
                            'confirmation' => array(
                                'type' => ConfirmationType::REDIRECT,
                                'confirmation_url' => Random::str(10),
                                'return_url' => Random::str(10),
                                'enforce' => false,
                            ),
                        ),
                        array(
                            'id' => Random::str(36),
                            'status' => PaymentStatus::SUCCEEDED,
                            'amount' => array(
                                'value' => Random::int(1, 100000),
                                'currency' => CurrencyCode::EUR,
                            ),
                            'description' => Random::str(20),
                            'created_at' => date(DATE_ATOM),
                            'payment_method' => array(
                                'type' => PaymentMethodType::QIWI,
                            ),
                            'paid' => true,
                            'refundable' => true,
                            'confirmation' => array(
                                'type' => ConfirmationType::REDIRECT,
                                'confirmation_url' => Random::str(10),
                            ),
                        ),
                    ),
                    'next_cursor' => uniqid(),
                ),
            ),
        );
    }
}