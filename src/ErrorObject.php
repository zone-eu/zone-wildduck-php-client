<?php

namespace Zone\Wildduck;

use Zone\Wildduck\Util\RequestOptions;
use Override;

/**
 * Class ErrorObject.
 *
 * @property string $charge For card errors, the ID of the failed charge.
 * @property string $code For some errors that could be handled
 *    programmatically, a short string indicating the error code reported.
 * @property string $decline_code For card errors resulting from a card issuer
 *    decline, a short string indicating the card issuer's reason for the
 *    decline if they provide one.
 * @property string $doc_url A URL to more information about the error code
 *    reported.
 * @property string $message A human-readable message providing more details
 *    about the error. For card errors, these messages can be shown to your
 *    users.
 * @property string $param If the error is parameter-specific, the parameter
 *    related to the error. For example, you can use this to display a message
 *    near the correct form field.
 * @property PaymentIntent $payment_intent The PaymentIntent object for errors
 *    returned on a request involving a PaymentIntent.
 * @property PaymentMethod $payment_method The PaymentMethod object for errors
 *    returned on a request involving a PaymentMethod.
 * @property SetupIntent $setup_intent The SetupIntent object for errors
 *    returned on a request involving a SetupIntent.
 * @property WildduckObject $source The source object for errors returned on a
 *    request involving a source.
 * @property string $type The type of error returned. One of
 *    `api_connection_error`, `api_error`, `authentication_error`,
 *    `card_error`, `idempotency_error`, `invalid_request_error`, or
 *    `rate_limit_error`.
 */
class ErrorObject extends WildduckObject
{
    /**
     * Possible string representations of an error's code.
     *
     * @see https://stripe.com/docs/error-codes
     */
    public const string CODE_ACCOUNT_ALREADY_EXISTS = 'account_already_exists';

    public const string CODE_ACCOUNT_COUNTRY_INVALID_ADDRESS = 'account_country_invalid_address';

    public const string CODE_ACCOUNT_INVALID = 'account_invalid';

    public const string CODE_ACCOUNT_NUMBER_INVALID = 'account_number_invalid';

    public const string CODE_ALIPAY_UPGRADE_REQUIRED = 'alipay_upgrade_required';

    public const string CODE_AMOUNT_TOO_LARGE = 'amount_too_large';

    public const string CODE_AMOUNT_TOO_SMALL = 'amount_too_small';

    public const string CODE_API_KEY_EXPIRED = 'api_key_expired';

    public const string CODE_BALANCE_INSUFFICIENT = 'balance_insufficient';

    public const string CODE_BANK_ACCOUNT_EXISTS = 'bank_account_exists';

    public const string CODE_BANK_ACCOUNT_UNUSABLE = 'bank_account_unusable';

    public const string CODE_BANK_ACCOUNT_UNVERIFIED = 'bank_account_unverified';

    public const string CODE_BITCOIN_UPGRADE_REQUIRED = 'bitcoin_upgrade_required';

    public const string CODE_CARD_DECLINED = 'card_declined';

    public const string CODE_CHARGE_ALREADY_CAPTURED = 'charge_already_captured';

    public const string CODE_CHARGE_ALREADY_REFUNDED = 'charge_already_refunded';

    public const string CODE_CHARGE_DISPUTED = 'charge_disputed';

    public const string CODE_CHARGE_EXCEEDS_SOURCE_LIMIT = 'charge_exceeds_source_limit';

    public const string CODE_CHARGE_EXPIRED_FOR_CAPTURE = 'charge_expired_for_capture';

    public const string CODE_COUNTRY_UNSUPPORTED = 'country_unsupported';

    public const string CODE_COUPON_EXPIRED = 'coupon_expired';

    public const string CODE_CUSTOMER_MAX_SUBSCRIPTIONS = 'customer_max_subscriptions';

    public const string CODE_EMAIL_INVALID = 'email_invalid';

    public const string CODE_EXPIRED_CARD = 'expired_card';

    public const string CODE_IDEMPOTENCY_KEY_IN_USE = 'idempotency_key_in_use';

    public const string CODE_INCORRECT_ADDRESS = 'incorrect_address';

    public const string CODE_INCORRECT_CVC = 'incorrect_cvc';

    public const string CODE_INCORRECT_NUMBER = 'incorrect_number';

    public const string CODE_INCORRECT_ZIP = 'incorrect_zip';

    public const string CODE_INSTANT_PAYOUTS_UNSUPPORTED = 'instant_payouts_unsupported';

    public const string CODE_INVALID_CARD_TYPE = 'invalid_card_type';

    public const string CODE_INVALID_CHARGE_AMOUNT = 'invalid_charge_amount';

    public const string CODE_INVALID_CVC = 'invalid_cvc';

    public const string CODE_INVALID_EXPIRY_MONTH = 'invalid_expiry_month';

    public const string CODE_INVALID_EXPIRY_YEAR = 'invalid_expiry_year';

    public const string CODE_INVALID_NUMBER = 'invalid_number';

    public const string CODE_INVALID_SOURCE_USAGE = 'invalid_source_usage';

    public const string CODE_INVOICE_NO_CUSTOMER_LINE_ITEMS = 'invoice_no_customer_line_items';

    public const string CODE_INVOICE_NO_SUBSCRIPTION_LINE_ITEMS = 'invoice_no_subscription_line_items';

    public const string CODE_INVOICE_NOT_EDITABLE = 'invoice_not_editable';

    public const string CODE_INVOICE_PAYMENT_INTENT_REQUIRES_ACTION = 'invoice_payment_intent_requires_action';

    public const string CODE_INVOICE_UPCOMING_NONE = 'invoice_upcoming_none';

    public const string CODE_LIVEMODE_MISMATCH = 'livemode_mismatch';

    public const string CODE_LOCK_TIMEOUT = 'lock_timeout';

    public const string CODE_MISSING = 'missing';

    public const string CODE_NOT_ALLOWED_ON_STANDARD_ACCOUNT = 'not_allowed_on_standard_account';

    public const string CODE_ORDER_CREATION_FAILED = 'order_creation_failed';

    public const string CODE_ORDER_REQUIRED_SETTINGS = 'order_required_settings';

    public const string CODE_ORDER_STATUS_INVALID = 'order_status_invalid';

    public const string CODE_ORDER_UPSTREAM_TIMEOUT = 'order_upstream_timeout';

    public const string CODE_OUT_OF_INVENTORY = 'out_of_inventory';

    public const string CODE_PARAMETER_INVALID_EMPTY = 'parameter_invalid_empty';

    public const string CODE_PARAMETER_INVALID_INTEGER = 'parameter_invalid_integer';

    public const string CODE_PARAMETER_INVALID_STRING_BLANK = 'parameter_invalid_string_blank';

    public const string CODE_PARAMETER_INVALID_STRING_EMPTY = 'parameter_invalid_string_empty';

    public const string CODE_PARAMETER_MISSING = 'parameter_missing';

    public const string CODE_PARAMETER_UNKNOWN = 'parameter_unknown';

    public const string CODE_PARAMETERS_EXCLUSIVE = 'parameters_exclusive';

    public const string CODE_PAYMENT_INTENT_AUTHENTICATION_FAILURE = 'payment_intent_authentication_failure';

    public const string CODE_PAYMENT_INTENT_INCOMPATIBLE_PAYMENT_METHOD = 'payment_intent_incompatible_payment_method';

    public const string CODE_PAYMENT_INTENT_INVALID_PARAMETER = 'payment_intent_invalid_parameter';

    public const string CODE_PAYMENT_INTENT_PAYMENT_ATTEMPT_FAILED = 'payment_intent_payment_attempt_failed';

    public const string CODE_PAYMENT_INTENT_UNEXPECTED_STATE = 'payment_intent_unexpected_state';

    public const string CODE_PAYMENT_METHOD_UNACTIVATED = 'payment_method_unactivated';

    public const string CODE_PAYMENT_METHOD_UNEXPECTED_STATE = 'payment_method_unexpected_state';

    public const string CODE_PAYOUTS_NOT_ALLOWED = 'payouts_not_allowed';

    public const string CODE_PLATFORM_API_KEY_EXPIRED = 'platform_api_key_expired';

    public const string CODE_POSTAL_CODE_INVALID = 'postal_code_invalid';

    public const string CODE_PROCESSING_ERROR = 'processing_error';

    public const string CODE_PRODUCT_INACTIVE = 'product_inactive';

    public const string CODE_RATE_LIMIT = 'rate_limit';

    public const string CODE_RESOURCE_ALREADY_EXISTS = 'resource_already_exists';

    public const string CODE_RESOURCE_MISSING = 'resource_missing';

    public const string CODE_ROUTING_NUMBER_INVALID = 'routing_number_invalid';

    public const string CODE_SECRET_KEY_REQUIRED = 'secret_key_required';

    public const string CODE_SEPA_UNSUPPORTED_ACCOUNT = 'sepa_unsupported_account';

    public const string CODE_SETUP_ATTEMPT_FAILED = 'setup_attempt_failed';

    public const string CODE_SETUP_INTENT_AUTHENTICATION_FAILURE = 'setup_intent_authentication_failure';

    public const string CODE_SETUP_INTENT_UNEXPECTED_STATE = 'setup_intent_unexpected_state';

    public const string CODE_SHIPPING_CALCULATION_FAILED = 'shipping_calculation_failed';

    public const string CODE_SKU_INACTIVE = 'sku_inactive';

    public const string CODE_STATE_UNSUPPORTED = 'state_unsupported';

    public const string CODE_TAX_ID_INVALID = 'tax_id_invalid';

    public const string CODE_TAXES_CALCULATION_FAILED = 'taxes_calculation_failed';

    public const string CODE_TESTMODE_CHARGES_ONLY = 'testmode_charges_only';

    public const string CODE_TLS_VERSION_UNSUPPORTED = 'tls_version_unsupported';

    public const string CODE_TOKEN_ALREADY_USED = 'token_already_used';

    public const string CODE_TOKEN_IN_USE = 'token_in_use';

    public const string CODE_TRANSFERS_NOT_ALLOWED = 'transfers_not_allowed';

    public const string CODE_UPSTREAM_ORDER_CREATION_FAILED = 'upstream_order_creation_failed';

    public const string CODE_URL_INVALID = 'url_invalid';

    /**
     * Refreshes this object using the provided values.
     *
     * @param array $values
     * @param array|null|string|Util\RequestOptions $opts
     * @param bool $partial defaults to false
     */
    #[Override]
    public function refreshFrom(array|object $values, array|null|RequestOptions|string $opts, bool $partial = false): void
    {
        // Unlike most other API resources, the API will omit attributes in
        // error objects when they have a null value. We manually set default
        // values here to facilitate generic error handling.
        $values = array_merge([
            'charge' => null,
            'code' => null,
            'decline_code' => null,
            'doc_url' => null,
            'message' => null,
            'param' => null,
            'payment_intent' => null,
            'payment_method' => null,
            'setup_intent' => null,
            'source' => null,
            'type' => null,
        ], $values);
        parent::refreshFrom($values, $opts, $partial);
    }
}
