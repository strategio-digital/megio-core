<?php

declare(strict_types=1);

namespace Megio\Http\Serializer;

/**
 * Map of Symfony validation messages to NEON translation keys.
 * Keys are exact Symfony messageTemplate strings, values are NEON keys (without domain prefix).
 */
final class ValidatorMessageMap
{
    public const array MAP = [
        // Boolean constraints
        'This value should be false.' => 'is_false',
        'This value should be true.' => 'is_true',

        // Type constraint
        'This value should be of type {{ type }}.' => 'type',

        // Blank / NotBlank / Null
        'This value should be blank.' => 'blank',
        'This value should not be blank.' => 'not_blank',
        'This value should not be null.' => 'not_null',
        'This value should be null.' => 'is_null',

        // Choice constraints
        'The value you selected is not a valid choice.' => 'choice_invalid',
        'You must select at least {{ limit }} choice.|You must select at least {{ limit }} choices.' => 'choice_min',
        'You must select at most {{ limit }} choice.|You must select at most {{ limit }} choices.' => 'choice_max',
        'One or more of the given values is invalid.' => 'choice_multiple_invalid',

        // Field constraints
        'This field was not expected.' => 'field_unexpected',
        'This field is missing.' => 'field_missing',

        // Date / Time
        'This value is not a valid date.' => 'date',
        'This value is not a valid datetime.' => 'datetime',
        'This value is not a valid time.' => 'time',

        // Email
        'This value is not a valid email address.' => 'email',

        // File constraints
        'The file could not be found.' => 'file_not_found',
        'The file is not readable.' => 'file_not_readable',
        'The file is too large ({{ size }} {{ suffix }}). Allowed maximum size is {{ limit }} {{ suffix }}.' => 'file_too_large',
        'The mime type of the file is invalid ({{ type }}). Allowed mime types are {{ types }}.' => 'file_invalid_mime',
        'The file is too large. Allowed maximum size is {{ limit }} {{ suffix }}.' => 'file_too_large_limit',
        'The file is too large.' => 'file_too_large_simple',
        'The file could not be uploaded.' => 'file_upload_failed',
        'An empty file is not allowed.' => 'file_empty',
        'The file was only partially uploaded.' => 'file_upload_partial',
        'No file was uploaded.' => 'file_upload_none',
        'No temporary folder was configured in php.ini.' => 'file_no_temp_folder',
        'Cannot write temporary file to disk.' => 'file_write_failed',
        'A PHP extension caused the upload to fail.' => 'file_upload_php_extension',
        'The extension of the file is invalid ({{ extension }}). Allowed extensions are {{ extensions }}.' => 'file_invalid_extension',
        'The filename is too long. It should have {{ filename_max_length }} character or less.|The filename is too long. It should have {{ filename_max_length }} characters or less.' => 'filename_too_long',
        'This filename does not match the expected charset.' => 'filename_charset',

        // Range / Comparison
        'This value should be {{ limit }} or less.' => 'range_max',
        'This value should be {{ limit }} or more.' => 'range_min',
        'This value should be between {{ min }} and {{ max }}.' => 'range',
        'This value should be equal to {{ compared_value }}.' => 'equal_to',
        'This value should be greater than {{ compared_value }}.' => 'greater_than',
        'This value should be greater than or equal to {{ compared_value }}.' => 'greater_than_or_equal',
        'This value should be identical to {{ compared_value_type }} {{ compared_value }}.' => 'identical_to',
        'This value should be less than {{ compared_value }}.' => 'less_than',
        'This value should be less than or equal to {{ compared_value }}.' => 'less_than_or_equal',
        'This value should not be equal to {{ compared_value }}.' => 'not_equal_to',
        'This value should not be identical to {{ compared_value_type }} {{ compared_value }}.' => 'not_identical_to',
        'This value should be a multiple of {{ compared_value }}.' => 'divisible_by',
        'The two values should be equal.' => 'two_values_equal',

        // Length constraints
        'This value is too long. It should have {{ limit }} character or less.|This value is too long. It should have {{ limit }} characters or less.' => 'length_max',
        'This value is too short. It should have {{ limit }} character or more.|This value is too short. It should have {{ limit }} characters or more.' => 'length_min',
        'This value should have exactly {{ limit }} character.|This value should have exactly {{ limit }} characters.' => 'length_exact',

        // General
        'This value is not valid.' => 'invalid',
        'This value should be a valid number.' => 'number',

        // URL
        'This value is not a valid URL.' => 'url',
        'This URL is missing a top-level domain.' => 'url_missing_tld',

        // Image constraints
        'This file is not a valid image.' => 'image_invalid',
        'The size of the image could not be detected.' => 'image_size_not_detected',
        'The image width is too big ({{ width }}px). Allowed maximum width is {{ max_width }}px.' => 'image_width_too_big',
        'The image width is too small ({{ width }}px). Minimum width expected is {{ min_width }}px.' => 'image_width_too_small',
        'The image height is too big ({{ height }}px). Allowed maximum height is {{ max_height }}px.' => 'image_height_too_big',
        'The image height is too small ({{ height }}px). Minimum height expected is {{ min_height }}px.' => 'image_height_too_small',
        'The image ratio is too big ({{ ratio }}). Allowed maximum ratio is {{ max_ratio }}.' => 'image_ratio_too_big',
        'The image ratio is too small ({{ ratio }}). Minimum ratio expected is {{ min_ratio }}.' => 'image_ratio_too_small',
        'The image is square ({{ width }}x{{ height }}px). Square images are not allowed.' => 'image_square_not_allowed',
        'The image is landscape oriented ({{ width }}x{{ height }}px). Landscape oriented images are not allowed.' => 'image_landscape_not_allowed',
        'The image is portrait oriented ({{ width }}x{{ height }}px). Portrait oriented images are not allowed.' => 'image_portrait_not_allowed',
        'The image file is corrupted.' => 'image_corrupted',
        'The image has too few pixels ({{ pixels }} pixels). Minimum amount expected is {{ min_pixels }} pixels.' => 'image_too_few_pixels',
        'The image has too many pixels ({{ pixels }} pixels). Maximum amount expected is {{ max_pixels }} pixels.' => 'image_too_many_pixels',

        // Video constraints
        'This file is not a valid video.' => 'video_invalid',
        'The size of the video could not be detected.' => 'video_size_not_detected',
        'The video width is too big ({{ width }}px). Allowed maximum width is {{ max_width }}px.' => 'video_width_too_big',
        'The video width is too small ({{ width }}px). Minimum width expected is {{ min_width }}px.' => 'video_width_too_small',
        'The video height is too big ({{ height }}px). Allowed maximum height is {{ max_height }}px.' => 'video_height_too_big',
        'The video height is too small ({{ height }}px). Minimum height expected is {{ min_height }}px.' => 'video_height_too_small',
        'The video has too few pixels ({{ pixels }} pixels). Minimum amount expected is {{ min_pixels }} pixels.' => 'video_too_few_pixels',
        'The video has too many pixels ({{ pixels }} pixels). Maximum amount expected is {{ max_pixels }} pixels.' => 'video_too_many_pixels',
        'The video ratio is too big ({{ ratio }}). Allowed maximum ratio is {{ max_ratio }}.' => 'video_ratio_too_big',
        'The video ratio is too small ({{ ratio }}). Minimum ratio expected is {{ min_ratio }}.' => 'video_ratio_too_small',
        'The video is square ({{ width }}x{{ height }}px). Square videos are not allowed.' => 'video_square_not_allowed',
        'The video is landscape oriented ({{ width }}x{{ height }}px). Landscape oriented videos are not allowed.' => 'video_landscape_not_allowed',
        'The video is portrait oriented ({{ width }}x{{ height }}px). Portrait oriented videos are not allowed.' => 'video_portrait_not_allowed',
        'The video file is corrupted.' => 'video_corrupted',
        'The video contains multiple streams. Only one stream is allowed.' => 'video_multiple_streams',
        'Unsupported video codec "{{ codec }}".' => 'video_unsupported_codec',
        'Unsupported video container "{{ container }}".' => 'video_unsupported_container',

        // Password
        "This value should be the user's current password." => 'password_current',
        'This password has been leaked in a data breach, it must not be used. Please use another password.' => 'password_leaked',
        'The password strength is too low. Please use a stronger password.' => 'password_strength',

        // Collection constraints
        'This collection should contain {{ limit }} element or more.|This collection should contain {{ limit }} elements or more.' => 'count_min',
        'This collection should contain {{ limit }} element or less.|This collection should contain {{ limit }} elements or less.' => 'count_max',
        'This collection should contain exactly {{ limit }} element.|This collection should contain exactly {{ limit }} elements.' => 'count_exact',
        'The number of elements in this collection should be a multiple of {{ compared_value }}.' => 'count_divisible_by',
        'This collection should contain only unique elements.' => 'unique',

        // Card / Banking
        'Invalid card number.' => 'card_invalid',
        'Unsupported card type or invalid card number.' => 'card_unsupported',
        'This is not a valid International Bank Account Number (IBAN).' => 'iban',
        'This is not a valid Business Identifier Code (BIC).' => 'bic',
        'This Business Identifier Code (BIC) is not associated with IBAN {{ iban }}.' => 'bic_iban_mismatch',

        // ISBN / ISSN / ISIN
        'This value is not a valid ISBN-10.' => 'isbn_10',
        'This value is not a valid ISBN-13.' => 'isbn_13',
        'This value is neither a valid ISBN-10 nor a valid ISBN-13.' => 'isbn',
        'This value is not a valid ISSN.' => 'issn',
        'This value is not a valid International Securities Identification Number (ISIN).' => 'isin',

        // Country / Language / Locale / Currency / Timezone
        'This value is not a valid language.' => 'language',
        'This value is not a valid locale.' => 'locale',
        'This value is not a valid country.' => 'country',
        'This value is not a valid currency.' => 'currency',
        'This value is not a valid timezone.' => 'timezone',

        // Positive / Negative
        'This value should be positive.' => 'positive',
        'This value should be either positive or zero.' => 'positive_or_zero',
        'This value should be negative.' => 'negative',
        'This value should be either negative or zero.' => 'negative_or_zero',

        // IP / Hostname / Network
        'This is not a valid IP address.' => 'ip',
        'This value is not a valid hostname.' => 'hostname',
        'This value is not a valid CIDR notation.' => 'cidr',
        'The value of the netmask should be between {{ min }} and {{ max }}.' => 'netmask_range',
        'This value is not a valid MAC address.' => 'mac_address',
        'The host could not be resolved.' => 'host_not_resolved',

        // UUID / ULID
        'This is not a valid UUID.' => 'uuid',
        'This is not a valid ULID.' => 'ulid',

        // JSON
        'This value should be valid JSON.' => 'json',

        // Already used
        'This value is already used.' => 'already_used',

        // Charset / Encoding
        'This value does not match the expected {{ charset }} charset.' => 'charset',
        'The detected character encoding is invalid ({{ detected }}). Allowed encodings are {{ encodings }}.' => 'encoding_invalid',

        // Expression
        'This value should be a valid expression.' => 'expression',

        // CSS
        'This value is not a valid CSS color.' => 'css_color',

        // AtLeastOneOf
        'This value should satisfy at least one of the following constraints:' => 'at_least_one_of',
        'Each element of this collection should satisfy its own set of constraints.' => 'at_least_one_of_collection',

        // Word count
        'This value is too short. It should contain at least one word.|This value is too short. It should contain at least {{ min }} words.' => 'word_count_min',
        'This value is too long. It should contain one word.|This value is too long. It should contain {{ max }} words or less.' => 'word_count_max',

        // Week
        'This value does not represent a valid week in the ISO 8601 format.' => 'week_invalid_format',
        'This value is not a valid week.' => 'week_invalid',
        'This value should not be before week "{{ min }}".' => 'week_min',
        'This value should not be after week "{{ max }}".' => 'week_max',

        // Twig
        'This value is not a valid Twig template.' => 'twig_invalid',

        // Security / Characters
        'This value contains characters that are not allowed by the current restriction-level.' => 'chars_not_allowed',
        'Using invisible characters is not allowed.' => 'invisible_chars',
        'Mixing numbers from different scripts is not allowed.' => 'mixed_scripts',
        'Using hidden overlay characters is not allowed.' => 'hidden_overlay',

        // Error
        'Error' => 'error',
    ];
}
