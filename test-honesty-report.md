# Test-Honesty Analysis Report

## Summary
- **Total Tests Analyzed**: 948
- **Hollow Tests** (score < 50%): 885
- **Honest Tests** (score ≥ 50%): 63
- **Hollow Ratio**: 93.4%

## Detailed Results

### tests/Unit/Security/SumexSecurityTest.php

#### it_restricts_sumex_remote_requests_to_https ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Unit/Security/FileSecurityHelperTest.php

#### it_rejects_unsafe_filenames ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_sanitizes_header_injection_and_uses_a_fallback_for_empty_output ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_sanitizes_document_numbers_to_filename_safe_characters ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_validates_database_ports_in_the_valid_range ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_confines_resolved_files_to_the_allowed_directory ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Unit/Security/CurrencySymbolXssTest.php

#### it_escapes_html_in_the_currency_symbol_for_before_placement ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_escapes_html_in_the_currency_symbol_for_afterspace_placement ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_escapes_html_in_the_currency_symbol_for_after_placement ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_escapes_an_attribute_breaking_currency_symbol ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_preserves_a_benign_currency_symbol ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Unit/Security/CryptorTest.php

#### it_round_trips_text_using_base64_output ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_round_trips_binary_data_without_multibyte_corruption ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_supports_hex_encoded_ciphertext ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_uses_a_fresh_iv_for_each_encryption ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_ciphertext_that_is_shorter_than_the_iv ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_round_trip_a_tampered_ciphertext_to_the_original_plaintext ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_hashes_and_verifies_passwords_with_the_crypt_wrapper ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_decodes_base64_encryption_keys_in_the_crypt_wrapper ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_correctly_decrypts_when_iv_bytes_are_extracted_properly ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_never_misextracts_the_iv_across_many_random_round_trips ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_empty_or_null_encrypted_passwords ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_correctly_extracts_iv_from_base64_with_binary_byte_count ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_never_calls_mb_string_functions_on_binary_ciphertext ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_keeps_pint_mb_str_functions_fixer_disabled ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Unit/Security/SecurityHelperTest.php

#### it_accepts_a_same_origin_referer ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_replaces_an_external_referer_with_the_safe_default ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_missing_get_csrf_token ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_accepts_a_matching_get_csrf_token ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_missing_post_csrf_token_instead_of_treating_null_as_equal ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_trusts_the_framework_check_on_a_post_whose_token_was_already_consumed ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: E
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_escapes_urls_for_html_output ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Unit/Security/CountryHelperTest.php

#### it_loads_a_valid_country_locale ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_falls_back_to_english_for_a_path_traversal_locale ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Unit/Security/MarkupSanitizerTest.php

#### it_keeps_safe_email_markup_and_removes_script_content ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_removes_external_images_from_email_markup ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_keeps_only_safe_pdf_footer_tags_and_strips_attributes ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_converts_pdf_footer_break_tags_and_handles_null ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Unit/Security/DatabaseConfigValidationTest.php

#### it_accepts_passwords_with_special_characters ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_passwords_with_control_characters ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_passwords_containing_a_single_quote ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_password_that_is_only_a_single_quote ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_empty_hostname ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_empty_username ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_empty_database ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_allows_empty_password ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_hostnames_containing_a_single_quote ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_usernames_containing_a_single_quote ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_database_names_containing_a_single_quote ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Unit/Security/IpSecurityHelperTest.php

#### it_generates_a_hexadecimal_token_with_the_requested_entropy ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_non_positive_token_length ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_generates_a_password_reset_token_with_256_bits_of_entropy ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_generates_a_bcrypt_compatible_salt ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Unit/Clients/ClientsTest.php

#### it_returns_the_matching_title_value ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_null_for_an_unknown_title ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_exposes_all_supported_titles_including_custom ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Unit/Payments/PaymentsTest.php

#### it_converts_standard_currency_amounts_to_minor_units ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_converts_zero_decimal_currency_amounts_without_scaling ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_converts_minor_units_back_to_major_units ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_non_positive_minor_unit_multiplier ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_creates_an_order_from_the_payment_information ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_generates_paypal_request_ids_with_context_and_uuid_format ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Unit/Settings/TaxRateDecimalPlacesProcessorTest.php

#### it_accepts_an_integer_within_range ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_sql_injection_payload ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_non_numeric_string ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_value_above_the_max_range ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_negative_value_below_the_min_range ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_float_masquerading_as_an_integer ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_reports_a_schema_change_only_when_the_value_actually_differs ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Unit/Settings/CustomTemplateKernelBootTest.php

#### it_populates_the_invoice_pdf_allowlist_constant_through_the_real_kernel ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_defines_all_four_allowlist_constants_after_boot ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_defines_the_env_helper_before_wiring_the_constants ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Unit/Settings/CustomTemplateAllowlistTest.php

#### it_lists_a_custom_invoice_pdf_template_configured_in_ipconfig ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_lists_a_custom_invoice_public_template_configured_in_ipconfig ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_lists_a_custom_quote_pdf_template_configured_in_ipconfig ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_lists_a_custom_quote_public_template_configured_in_ipconfig ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_keeps_built_in_templates_alongside_a_custom_one ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_lists_multiple_comma_separated_custom_templates ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_only_built_ins_when_no_custom_templates_are_configured ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_path_traversal_custom_template_name ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_php_extension_custom_template_name ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_keeps_the_valid_name_and_drops_the_invalid_one_from_a_mixed_list ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_wires_all_four_allowlist_constants_from_ipconfig_through_the_single_bootstrap ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Unit/Settings/SessionSavePathResolverTest.php

#### it_falls_back_to_the_system_temp_dir_when_the_value_is_an_empty_string ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_falls_back_to_the_system_temp_dir_when_the_value_is_whitespace_only ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_falls_back_to_the_system_temp_dir_when_the_value_is_null ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_an_explicit_path_unchanged ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_trims_a_trailing_slash_from_an_explicit_path ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_honours_a_caller_supplied_fallback_for_a_blank_value ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_never_returns_an_empty_string_or_a_non_directory_for_a_blank_value ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_routes_the_sess_save_path_config_through_the_resolver ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_loads_the_resolver_from_the_single_kernel_boot_path ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_keeps_the_shipped_example_config_from_resolving_to_a_broken_session_path ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_resolves_a_real_directory_when_config_php_boots_with_an_empty_sess_save_path ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Unit/Services/UserAuthorizationServiceTest.php

#### it_allows_primary_admin_to_edit_any_user ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_allows_any_user_to_edit_themselves ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_prevents_secondary_admin_from_editing_other_users ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_prevents_guests_from_editing_others ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_prevents_secondary_admin_from_changing_user_types ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_allows_primary_admin_to_change_user_types ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_prevents_self_escalation_during_self_edit ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_prevents_primary_admin_type_change_during_self_edit ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_delegates_form_view_to_edit_authorization ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_enforces_complete_authorization_matrix ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Unit/Users/MdlUsersTest.php

#### it_returns_true_for_user_id_1_as_integer ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_true_for_user_id_1_as_string ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_true_for_user_id_01_as_string ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_true_for_user_id_1abc_as_string ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_true_when_user_id_is_boolean_true ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_false_for_user_id_2_as_integer ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_false_for_user_id_2_as_string ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_false_for_user_id_0 ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_false_when_user_id_is_boolean_false ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_false_for_negative_user_id ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_false_for_null ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_false_for_empty_string ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_false_for_whitespace_string ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_false_for_non_numeric_string ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_false_for_float_1_point_0 ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_false_for_float_1_point_5 ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_false_for_very_large_user_id ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_false_for_string_with_only_whitespace ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Unit/Invoices/InvoicesTest.php

#### it_calculates_recursive_mod10_checksums ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_builds_a_valid_isr_code_line ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_an_invalid_subscriber_number ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_an_amount_above_the_isr_limit ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_prefers_configured_recipient_and_invoice_bank_details ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_falls_back_to_invoice_identity_and_settings_for_missing_bank_details ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Unit/Core/QontoClientTest.php

#### it_exposes_client_code_and_name ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_provides_default_settings_with_all_endpoints ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_authenticates_when_required_settings_are_present ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_authentication_without_access_token ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_authentication_without_api_base_url ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_the_access_token_from_fetch_token ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_attaches_the_bearer_token_to_every_request ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_adds_the_staging_token_header_when_configured ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_omits_the_staging_header_when_not_configured ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_builds_the_incoming_invoices_url_with_query_filters ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_always_scopes_incoming_invoices_to_the_e_invoicing_source ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_interpolates_the_external_id_into_the_status_endpoint ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_reads_status_from_the_json_api_attributes_shape ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_falls_back_to_the_envelope_status_when_body_has_no_status ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_sends_an_invoice_through_the_import_then_send_by_einvoice_pipeline ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_throws_when_the_document_to_send_is_missing ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_stops_after_import_when_no_client_invoice_id_is_returned ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_the_import_failure_without_attempting_send_by_einvoice ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_requires_the_status_endpoint_setting ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_threads_the_invoice_number_onto_the_metadata ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_sets_a_null_invoice_number_when_the_invoice_has_none ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_reports_the_provider_as_reachable_when_the_incoming_list_call_answers ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_reports_the_provider_as_unreachable_when_authentication_fails ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_reports_the_provider_as_unreachable_when_the_incoming_endpoint_is_not_configured ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Unit/Core/SuperPdpClientTest.php

#### it_calls_fetch_token_with_the_configured_credentials ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 5

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_authenticate_when_client_id_is_missing ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_authenticate_when_client_secret_is_missing ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_authenticate_when_token_url_is_missing ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_authenticate_when_the_token_response_contains_no_access_token ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_propagates_a_fetch_token_network_exception ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_send_when_the_invoice_file_does_not_exist ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_send_when_no_access_token_is_present ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_uploads_the_invoice_pdf_as_a_raw_application_pdf_body ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 6

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_adds_the_disable_pre_check_flag_to_the_request_query ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_interpolates_the_invoice_id_into_the_status_url ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_get_status_when_the_endpoint_setting_is_missing ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_appends_filters_as_a_query_string_when_receiving_invoices ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_receive_when_the_endpoint_setting_is_missing ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fetches_invoice_events_with_a_get_request ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_get_events_when_the_endpoint_setting_is_missing ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_superpdp_as_the_provider_code ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_includes_all_required_oauth_keys_in_default_settings ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_metadata_unchanged_from_build_invoice_payload ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_reports_the_provider_as_reachable_when_the_read_call_answers ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_reports_the_provider_as_unreachable_when_the_token_request_fails ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 5

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_reports_the_provider_as_unreachable_when_settings_are_missing ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Unit/Core/LetsPeppolApiClientTest.php

#### it_stores_settings_via_configure ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_calls_fetch_token_with_the_configured_credentials ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_stores_the_access_token_returned_by_fetch_token ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_throws_when_fetch_token_returns_no_access_token ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_propagates_a_network_exception_from_fetch_token ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_passes_the_bearer_token_to_send ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_passes_method_url_payload_and_multipart_flag_to_send ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: E
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_appends_a_query_string_to_the_url_before_calling_send ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_the_response_envelope_from_send ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_joins_base_url_and_endpoint_path ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_interpolates_the_id_placeholder_in_the_endpoint ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_url_encodes_the_id_when_interpolating ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_strips_duplicate_slashes_between_base_and_path ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_uses_the_token_from_authenticate_in_a_subsequent_request ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_sends_multiple_requests_all_carrying_the_same_token ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Unit/Core/LetsPeppolScenarioTest.php

#### it_looks_up_a_reachable_participant_after_authentication ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 6

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_reachable_false_when_participant_is_not_on_the_peppol_network ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_propagates_a_not_found_response_when_participant_id_does_not_exist ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 5

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_sends_the_bearer_token_on_the_participant_lookup_request ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_the_access_token_string_via_fetch_token ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_throws_when_fetch_token_receives_no_access_token ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_sends_an_invoice_and_returns_the_external_id ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_sends_the_bearer_token_on_the_invoice_send_request ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_throws_when_the_invoice_document_file_does_not_exist ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_a_validation_error_when_the_api_rejects_the_invoice ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 5

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_an_auth_error_when_the_token_is_rejected_during_send ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_delivered_status_after_a_successful_send ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 5

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_rejected_status_when_the_recipient_rejects_the_invoice ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_sends_the_bearer_token_on_the_status_check_request ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_completes_the_full_peppol_flow_in_order ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 6

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_retrieves_incoming_invoices_from_the_peppol_network ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 5

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_uses_the_same_token_for_all_requests_within_one_authentication_session ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_retrieves_transmission_status_and_attaches_the_external_id ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_sends_a_credit_note_and_returns_the_external_id ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_retrieves_credit_note_status_and_attaches_the_external_id ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_reports_the_provider_as_reachable_after_a_successful_read ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_reports_the_provider_as_unreachable_when_the_token_request_fails ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 5

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Unit/Core/IntegrationClientRegistryTest.php

#### it_discovers_the_bundled_providers ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_indexes_every_provider_under_its_own_client_code ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_only_registers_integration_client_implementations ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_resolves_a_provider_instance_by_client_code ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_a_fresh_instance_on_each_resolution ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_throws_for_an_unknown_provider_code ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Unit/Core/IntegrationsViewLanguageTest.php

#### it_defines_every_translation_key_used_by_the_integration_views ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_defines_the_navigation_entry_point_labels ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Unit/Core/MerchantResponseEnumsTest.php

#### it_maps_status_to_success_state ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_backs_every_status_with_the_expected_string ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_resolves_a_status_from_its_backing_value ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_backs_response_types_with_stable_values ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_backs_directions_with_stable_values ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_backs_provider_drivers_with_stable_values ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_matches_provider_driver_codes_to_the_client_codes ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: E
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_backs_request_methods_with_http_verbs ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: E
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_exposes_the_bis_billing_invoice_document_id ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_keeps_all_document_type_ids_unique ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Unit/Libraries/Gateways/PaypalRequestExecutorTest.php

#### it_executes_successful_request_and_returns_response ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_handles_client_exception_and_returns_error ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_handles_invalid_argument_exception ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_catches_throwable_exceptions ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_logs_action_messages ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_handles_multiple_sequential_requests ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Unit/Libraries/Gateways/StripeMinorUnitTest.php

#### it_uses_a_multiplier_of_one_for_zero_decimal_currencies ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_uses_a_multiplier_of_one_hundred_for_decimal_currencies ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_converts_a_decimal_currency_balance_to_integer_cents ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_sends_a_zero_decimal_currency_balance_unscaled ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rounds_to_the_nearest_minor_unit ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_multiplier_below_one ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_converts_stripe_cents_back_to_a_major_amount ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_leaves_a_zero_decimal_currency_amount_unscaled_on_the_way_back ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_round_trips_a_decimal_currency_balance ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_multiplier_below_one_on_the_way_back ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Unit/Libraries/Gateways/PaypalResponseExtractorTest.php

#### it_extracts_capture_data_from_valid_response ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: F
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);

#### it_returns_null_when_capture_data_missing ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: F
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);

#### it_returns_null_for_a_structurally_invalid_response ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: F
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);

#### it_extracts_capture_status_uppercase ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_handles_pending_status ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_null_for_missing_capture_status ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: F
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);

#### it_returns_null_for_invalid_response_structure_status ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: F
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);

#### it_extracts_invoice_id_from_capture_data ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_extracts_invoice_id_from_full_response ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_null_for_missing_invoice_id ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: F
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);

#### it_handles_malformed_response_for_invoice_id ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: F
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);

#### it_extracts_amount_and_currency ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_handles_missing_amount_data ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: F
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);

#### it_extracts_processor_response_code ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_default_for_missing_processor_response_code ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_handles_edge_case_empty_amount_string ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_handles_edge_case_null_currency_code ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Unit/Libraries/Gateways/StripeResponseExtractorTest.php

#### it_reports_a_paid_session_as_paid ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_reports_an_unpaid_session_as_not_paid ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_treats_a_null_session_as_not_paid ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_extracts_the_payment_intent_id ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_null_when_the_payment_intent_is_absent_or_empty ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: F
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);

#### it_extracts_the_invoice_key_from_the_client_reference_id ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: F
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);

#### it_upper_cases_the_currency_and_defaults_to_empty_string ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_extracts_the_minor_unit_total_as_an_integer ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Unit/Libraries/Gateways/StripeApiClientTest.php

#### it_reports_stripe_as_reachable_when_the_session_list_call_succeeds ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_reports_stripe_as_unreachable_on_an_authentication_error ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_reports_stripe_as_unreachable_on_a_server_error ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Unit/Setup/DatabaseConfigFileTest.php

#### it_writes_and_reads_config_with_special_characters ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_applies_no_escaping_at_all_when_writing ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_round_trips_a_password_containing_only_special_characters ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Unit/Setup/SqlHelperTest.php

#### it_splits_on_top_level_semicolons_only ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_split_on_semicolons_inside_strings_or_comments ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_keeps_the_final_statement_when_earlier_comments_contain_multibyte_characters ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_preserves_every_statement_of_the_real_consolidated_1_8_0_migration ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Scripts/ClassCoverageInventoryTest.php

#### it_builds_an_inventory_for_testable_application_classes ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_keeps_the_committed_markdown_inventory_in_sync ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Scripts/ParsePhpstanResultsTest.php

#### it_categorizes_return_type_errors ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_categorizes_return_type_errors_case_insensitively ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_categorizes_method_errors_for_undefined_method ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_categorizes_method_errors_when_message_contains_method_keyword ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_categorizes_property_errors ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_categorizes_type_errors_for_type_mismatch ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_categorizes_type_errors_when_only_type_keyword_present ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_falls_back_to_other_errors_for_unrecognized_messages ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_falls_back_to_other_errors_for_empty_message ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_prioritizes_return_type_over_method_when_both_keywords_present ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_classifies_message_with_property_and_method_as_method_error ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_human_readable_label_for_known_category ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_unknown_for_unrecognized_category ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_short_message_unchanged ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_truncates_message_exceeding_default_max_length ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_truncates_message_to_custom_max_length ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_truncate_message_at_exact_max_length ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_collapses_multiple_whitespace_characters ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_trims_leading_and_trailing_whitespace ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_handles_multibyte_characters_correctly ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_strips_project_root_prefix_from_absolute_path ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_normalizes_windows_backslashes_to_forward_slashes ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_path_unchanged_when_no_prefix_matches ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_strips_cwd_prefix_as_fallback ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Security/DevelopMergeSecurityTest.php

#### it_defines_the_sumex_storage_folder_outside_the_public_web_root ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_writes_the_sumex_xml_to_the_non_web_accessible_storage_folder ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_denies_direct_web_access_to_the_uploads_import_directory ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Security/SecurityRegressionTest.php

#### it_denies_a_guest_access_to_another_clients_invoice_pdf ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_denies_a_guest_access_to_another_clients_quote_pdf ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_mark_an_invoice_sent_from_a_forged_generate_pdf_get ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_marks_an_invoice_sent_only_with_a_matching_generate_pdf_csrf_token ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_mark_a_quote_sent_from_a_forged_generate_pdf_get ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_marks_a_quote_sent_only_with_a_matching_generate_pdf_csrf_token ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_path_traversal_payload_in_the_file_download_endpoint ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_null_byte_injection_in_the_file_download_endpoint ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_path_traversal_payload_in_the_file_delete_endpoint ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: E
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_stores_a_crafted_setting_key_safely_without_breaking_the_table ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, D, E
- **Assertions**: 4

#### it_stores_a_setting_key_with_html_characters_as_literal_text ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_path_traversal_value_for_the_invoice_logo_setting ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: E
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_path_traversal_value_for_the_login_logo_setting ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_falls_back_to_the_default_template_for_a_path_traversal_pdf_template_name ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_falls_back_to_the_default_template_for_an_unlisted_pdf_template_name ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Security/CsrfDeleteSecurityTest.php

#### it_requires_post_validation_for_delete_endpoints ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: E
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_link_to_delete_endpoints_with_get_anchors ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_includes_csrf_tokens_in_post_forms ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: E
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Projects/TasksControllerTest.php

#### it_lists_every_task ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_creates_a_task ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_create_without_task_name ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_create_without_task_price ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_create_without_task_finish_date ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_renders_the_edit_form_for_the_requested_task_only ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_updates_a_task ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 5

#### it_fails_to_update_without_task_name ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_update_without_task_price ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_update_without_task_finish_date ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_deletes_a_task ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 5

#### it_still_deletes_a_task_when_csrf_protection_is_on_and_the_token_is_valid ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_delete_a_task_when_the_csrf_token_is_missing ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_a_guest_to_login_and_leaks_no_task ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Projects/TasksAjaxControllerTest.php

#### it_renders_the_task_lookup_modal_with_no_invoice ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_processes_a_task_selection ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_an_empty_result_when_no_task_ids_are_selected ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_requires_an_ajax_request ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Projects/ServicesControllerTest.php

#### it_lists_every_service ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_creates_a_service ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, D, E
- **Assertions**: 4

#### it_fails_to_create_a_service_without_a_name ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_create_a_service_whose_name_is_already_taken ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_updates_a_service ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 5

#### it_fails_to_update_a_service_without_a_name ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_creates_a_service_and_pins_it_to_a_client ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, D, E
- **Assertions**: 4

#### it_404s_when_pinning_a_service_to_a_client_that_does_not_exist ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_resolves_a_tagged_invoices_service_name_in_the_filtered_invoice_table ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_deletes_a_service_and_its_client_links ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 5

#### it_does_not_delete_a_service_on_a_plain_get_request ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_a_guest_to_login_and_leaks_no_service ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Projects/ProjectsControllerTest.php

#### it_lists_every_project ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_creates_a_project ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_create_without_project_name ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_renders_the_edit_form_for_the_requested_project_only ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_updates_a_project ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 5

#### it_fails_to_update_without_project_name ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_deletes_a_project ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 5

#### it_orphans_rather_than_deletes_the_tasks_of_a_deleted_project ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_still_deletes_a_project_when_csrf_protection_is_on_and_the_token_is_valid ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_delete_a_project_when_the_csrf_token_is_missing ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_a_guest_to_login_and_leaks_no_project ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Clients/ClientsControllerTest.php

#### it_lists_every_active_client ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_creates_a_client ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_create_without_client_name ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_renders_the_edit_form_for_the_requested_client_only ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_updates_a_client ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 5

#### it_fails_to_update_without_client_name ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_deletes_a_client ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 5

#### it_still_deletes_a_client_when_csrf_protection_is_on_and_the_token_is_valid ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_delete_a_client_when_the_csrf_token_is_missing ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_duplicate_client_name_and_surname_on_create ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_a_guest_to_login_and_leaks_no_client ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Clients/UserClientsControllerTest.php

#### it_lists_every_client_assigned_to_a_user ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_assigns_a_client_to_a_user ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_assign_without_client_id ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_assign_without_user_id ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_unassigns_a_client_from_a_user ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 5

#### it_still_unassigns_when_csrf_protection_is_on_and_the_token_is_valid ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_unassign_when_the_csrf_token_is_missing ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_blocks_a_non_admin_from_unassigning_a_client ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_a_guest_to_login_and_leaks_no_assignment ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: C, E
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Clients/ClientsAjaxControllerTest.php

#### it_finds_active_clients_matching_the_query ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_excludes_inactive_clients_from_name_query ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_an_empty_result_for_name_query_with_no_query ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_treats_name_query_input_as_a_literal_search_term ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_up_to_five_latest_active_clients ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_escapes_client_names_returned_by_get_latest ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_saves_a_valid_permissive_search_preference ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_an_invalid_permissive_search_preference_value ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_saves_a_client_note_with_all_required_fields ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, D, E
- **Assertions**: 4

#### it_fails_to_save_a_client_note_without_client_id ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_save_a_client_note_without_client_note_text ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_deletes_an_existing_client_note ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_delete_anything_for_a_nonexistent_note_id ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_loads_notes_for_a_client ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_requires_an_ajax_request ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Payments/GatewayPaymentRaceTest.php

#### it_does_not_double_record_when_two_distinct_stripe_callbacks_race ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_double_record_when_two_distinct_paypal_captures_race ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_keeps_one_row_when_the_same_stripe_intent_is_replayed_concurrently ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_still_records_a_single_full_balance_payment ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 5

### tests/Feature/Payments/PaymentsAjaxControllerTest.php

#### it_adds_a_payment_with_all_required_fields ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_add_a_payment_without_invoice_id ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_add_a_payment_without_payment_date ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_add_a_payment_without_payment_amount ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_add_a_payment_exceeding_the_invoice_balance ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_renders_the_add_payment_modal ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_requires_an_ajax_request ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Payments/StripeFlowTest.php

#### it_returns_404_for_a_non_post_checkout_session_request ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_404_for_checkout_session_on_an_unknown_invoice_key ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_404_for_checkout_session_on_a_draft_invoice ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_checkout_session_for_an_already_paid_invoice_without_calling_stripe ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_creates_a_checkout_session_for_a_payable_invoice ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_sends_a_jpy_invoice_total_as_100_minor_units_to_stripe_checkout ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_records_a_paid_callback_and_creates_a_payment ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_duplicate_a_payment_for_an_already_processed_payment_intent ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: B
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_record_a_payment_when_the_invoice_is_already_fully_paid ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_callback_whose_currency_does_not_match_the_gateway_setting ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_callback_whose_amount_is_short_of_the_invoice_balance ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_record_a_payment_for_an_unpaid_callback ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_records_an_error_response_when_the_callback_invoice_is_not_guest_visible ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Payments/PaymentInformationControllerTest.php

#### it_returns_a_successful_response_or_redirect ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_a_guest_to_login ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Payments/PaymentInformationFormTest.php

#### it_redirects_for_an_unknown_invoice_key ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_for_a_draft_invoice_key ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_404_for_an_already_paid_invoice_when_unauthenticated ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_renders_the_form_for_a_payable_invoice ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_expose_php_errors_for_an_already_paid_invoice ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Payments/PaymentProviderAllowlistTest.php

#### it_returns_200_when_accessing_the_payment_form_without_a_provider ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_404_for_an_unknown_payment_provider_segment ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_404_for_an_internal_controller_method_name_as_provider ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_404_for_a_path_traversal_attempt_as_provider ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Payments/GuestPaymentsControllerTest.php

#### it_redirects_an_unauthenticated_request_to_login ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_denies_an_admin_session_guest_type_access ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_403_for_a_guest_user_with_no_assigned_clients ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_lists_only_payments_for_the_guests_own_client ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_expose_php_errors ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Payments/PaymentMethodsControllerTest.php

#### it_lists_every_payment_method ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_creates_a_payment_method ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 5

#### it_fails_to_create_without_payment_method_name ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_renders_the_edit_form_for_the_requested_payment_method_only ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_updates_a_payment_method ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 5

#### it_fails_to_update_without_payment_method_name ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_deletes_a_payment_method ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 5

#### it_still_deletes_a_payment_method_when_csrf_protection_is_on_and_the_token_is_valid ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_delete_a_payment_method_when_the_csrf_token_is_missing ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_duplicate_payment_method_name_on_create ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: B
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_a_guest_to_login_and_leaks_no_payment_method ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Payments/PaypalControllerTest.php

#### it_returns_a_successful_response_or_redirect ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_a_guest_to_login ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Payments/PaymentsFeatureTest.php

#### it_lists_payments ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_creates_a_payment_and_links_it_to_the_invoice ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, D, E
- **Assertions**: 4

#### it_renders_the_edit_payment_form_showing_existing_amount ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_updates_a_payment ✓ HONEST
- **Score**: 66.7% (4/6 categories)
- **Categories Used**: A, B, D, E
- **Assertions**: 6

#### it_deletes_a_payment ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 5

#### it_fails_to_create_without_invoice_id ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: B, E
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_create_without_payment_amount ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: B, E
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_create_without_payment_date ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: B, E
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_creates_manual_payment_with_null_external_id ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_an_unauthenticated_visitor_away_from_the_payments_list ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Payments/StripeControllerTest.php

#### it_returns_a_successful_response_or_redirect ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_a_guest_to_login ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Payments/PaypalFlowTest.php

#### it_returns_404_for_a_non_post_create_order_request ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_404_for_create_order_on_an_unknown_invoice_key ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_404_for_create_order_on_a_draft_invoice ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_create_order_for_an_already_paid_invoice_without_calling_paypal ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_creates_a_paypal_order_for_a_payable_invoice ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_500_when_paypal_returns_malformed_json_for_create_order ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_500_when_paypal_response_is_missing_the_order_id ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_404_for_a_non_post_capture_payment_request ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_records_a_completed_capture_and_creates_a_payment ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_records_a_pending_capture_as_a_payment_with_a_pending_note ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_duplicate_a_payment_for_an_already_processed_capture_id ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: B
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_record_a_payment_when_the_invoice_is_already_fully_paid ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_capture_whose_currency_does_not_match_the_gateway_setting ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_capture_whose_amount_is_short_of_the_invoice_balance ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_records_a_declined_capture_as_an_unsuccessful_merchant_response ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 4

#### it_throws_and_records_nothing_when_the_captured_invoice_is_not_guest_visible ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Products/FamiliesControllerTest.php

#### it_lists_every_family ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_creates_a_family ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 5

#### it_fails_to_create_without_family_name ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_renders_the_edit_form_for_the_requested_family_only ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_updates_a_family ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 5

#### it_fails_to_update_without_family_name ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_deletes_a_family ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 5

#### it_still_deletes_a_family_when_csrf_protection_is_on_and_the_token_is_valid ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_delete_a_family_when_the_csrf_token_is_missing ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_duplicate_family_name_on_create ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: B
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_a_guest_to_login_and_leaks_no_family ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Products/UnitsControllerTest.php

#### it_lists_every_unit ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_creates_a_unit ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 5

#### it_fails_to_create_without_unit_name ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 5

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_create_without_unit_name_plrl ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 5

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_renders_the_edit_form_for_the_requested_unit_only ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_updates_a_unit ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 5

#### it_fails_to_update_without_unit_name ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_update_without_unit_name_plrl ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_deletes_a_unit ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 5

#### it_still_deletes_a_unit_when_csrf_protection_is_on_and_the_token_is_valid ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_delete_a_unit_when_the_csrf_token_is_missing ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_duplicate_unit_name_on_create ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_a_guest_to_login_and_leaks_no_unit ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Products/ProductsControllerTest.php

#### it_lists_every_product ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_creates_a_product ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 5

#### it_fails_to_create_without_product_name ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_create_without_product_price ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_renders_the_edit_form_for_the_requested_product_only ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_updates_a_product ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 5

#### it_fails_to_update_without_product_name ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_update_without_product_price ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_deletes_a_product ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 5

#### it_still_deletes_a_product_when_csrf_protection_is_on_and_the_token_is_valid ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_delete_a_product_when_the_csrf_token_is_missing ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_a_guest_to_login_and_leaks_no_product ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Products/ProductsAjaxControllerTest.php

#### it_renders_the_full_lookup_modal_with_no_filters ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_filters_the_lookup_table_by_product_name ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_processes_a_product_selection ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: C, E
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_an_empty_result_when_no_product_ids_are_selected ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Quotes/QuotesAjaxControllerTest.php

#### it_creates_a_quote ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_create_a_quote_without_client_id ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: B
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_create_a_quote_without_quote_date_created ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: B
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_create_a_quote_without_invoice_group_id ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: B
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_refuses_quote_creation_for_a_guest ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: B
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Quotes/QuotesControllerTest.php

#### it_lists_every_quote ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_shows_a_single_quote ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_deletes_a_quote ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 5

#### it_still_deletes_a_quote_when_csrf_protection_is_on_and_the_token_is_valid ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_delete_a_quote_when_the_csrf_token_is_missing ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_removes_a_tax_rate_from_a_quote ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 5

#### it_redirects_a_guest_to_login_and_leaks_no_quote ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Invoices/InvoiceGroupsControllerTest.php

#### it_lists_every_invoice_group ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_creates_an_invoice_group ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 5

#### it_fails_to_create_without_invoice_group_name ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 5

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_create_without_invoice_group_identifier_format ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 5

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_create_without_invoice_group_next_id ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 5

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_create_without_invoice_group_left_pad ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 5

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_renders_the_edit_form_for_the_requested_invoice_group_only ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_updates_an_invoice_group ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 5

#### it_fails_to_update_without_invoice_group_name ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_update_without_invoice_group_identifier_format ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_update_without_invoice_group_next_id ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_update_without_invoice_group_left_pad ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_deletes_an_invoice_group ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 5

#### it_still_deletes_an_invoice_group_when_csrf_protection_is_on_and_the_token_is_valid ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_delete_an_invoice_group_when_the_csrf_token_is_missing ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_a_guest_to_login_and_leaks_no_invoice_group ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Invoices/CronControllerTest.php

#### it_returns_a_successful_response_or_redirect ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_a_guest_to_login ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Invoices/CronRecurControllerTest.php

#### it_returns_500_for_a_wrong_cron_key ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 5

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_500_for_a_missing_cron_key ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_generates_a_due_recurring_invoice_with_the_correct_cron_key ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 5

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_generate_an_invoice_for_a_not_yet_due_recurring_invoice ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: B
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_generate_an_invoice_for_an_expired_recurring_series ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: B
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Invoices/InvoicesAjaxControllerTest.php

#### it_creates_an_invoice_with_all_required_fields ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_create_an_invoice_without_client_id ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_create_an_invoice_without_invoice_date_created ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_create_an_invoice_without_invoice_group_id ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_create_an_invoice_without_invoice_time_created ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_create_an_invoice_without_user_id ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_saves_an_invoice_with_all_required_fields ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_save_an_invoice_without_invoice_date_due ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_save_an_invoice_without_invoice_date_created ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_an_invoice_number_with_unsafe_characters ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_changes_the_invoices_user ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_change_the_invoices_user_for_an_unknown_user_id ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_changes_the_invoices_client ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_change_the_invoices_client_for_an_unknown_client_id ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_deletes_an_existing_invoice_item ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_delete_anything_for_a_nonexistent_item_id ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_save_an_invoice_tax_rate_without_invoice_id ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_creates_a_recurring_invoice_with_all_required_fields ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_create_a_recurring_invoice_without_recur_start_date ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_copies_an_invoice ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_copy_an_invoice_without_client_id ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_creates_a_credit_invoice ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_gets_an_item ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_gets_a_recur_start_date ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_requires_an_ajax_request ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_renders_the_create_invoice_modal ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_renders_the_create_recurring_modal ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_renders_the_create_credit_modal ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_saves_an_invoice_on_the_first_attempt ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_persists_a_save_that_immediately_follows_a_create ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, D, E
- **Assertions**: 4

### tests/Feature/Invoices/InvoicesControllerTest.php

#### it_lists_every_invoice ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_shows_a_single_invoice ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_deletes_a_draft_invoice ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 5

#### it_refuses_to_delete_a_sent_invoice_while_deletion_is_disabled ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_refuses_to_delete_a_paid_invoice_while_deletion_is_disabled ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_deletes_a_sent_invoice_when_global_invoice_deletion_is_enabled ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 5

#### it_still_deletes_a_draft_invoice_when_csrf_protection_is_on_and_the_token_is_valid ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_delete_an_invoice_when_the_csrf_token_is_missing ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_removes_a_tax_rate_from_an_invoice ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 5

#### it_does_not_remove_an_invoice_tax_rate_when_the_csrf_token_is_missing ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_a_guest_to_login_and_leaks_no_invoice ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Invoices/RecurringControllerTest.php

#### it_lists_every_recurring_schedule ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_creates_a_recurring_schedule_from_an_invoice_and_shows_it_in_the_list ✓ HONEST
- **Score**: 66.7% (4/6 categories)
- **Categories Used**: A, C, D, E
- **Assertions**: 5

#### it_stops_a_recurring_schedule ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_stop_a_recurring_schedule_on_a_plain_get_request ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, D, E
- **Assertions**: 4

#### it_deletes_a_recurring_schedule ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 5

#### it_does_not_delete_a_recurring_schedule_on_a_plain_get_request ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_still_deletes_a_recurring_schedule_when_csrf_protection_is_on_and_the_token_is_valid ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_delete_a_recurring_schedule_when_the_csrf_token_is_missing ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_a_guest_to_login_and_leaks_no_recurring_schedule ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Invoices/GuestViewControllerTest.php

#### it_returns_404_for_an_empty_invoice_key ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_404_for_an_unknown_invoice_key ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_404_for_a_draft_invoice_key ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_renders_a_guest_visible_invoice ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_404_for_an_empty_quote_key ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_404_for_an_unknown_quote_key ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_404_for_a_draft_quote_key ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_renders_a_guest_visible_quote ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_404_for_a_non_post_approve_quote_request ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, C, D
- **Assertions**: 4

#### it_denies_approve_quote_for_an_unauthenticated_guest ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, C, D
- **Assertions**: 4

#### it_denies_approving_a_quote_belonging_to_a_different_client ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, C, D
- **Assertions**: 4

#### it_approves_a_quote_for_its_own_client ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_denies_rejecting_a_quote_belonging_to_a_different_client ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, C, D
- **Assertions**: 4

#### it_rejects_a_quote_for_its_own_client ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_silently_produces_no_invoice_pdf_for_an_unknown_key ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_404_for_sumex_pdf_when_the_invoice_has_no_sumex_id ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_404_for_quote_pdf_on_an_unknown_key ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Invoices/GuestGetControllerTest.php

#### it_returns_an_empty_response_for_show_files_with_no_key ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_an_empty_response_for_show_files_on_a_draft_invoice ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_an_empty_response_for_show_files_with_no_uploads ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_lists_uploaded_files_for_a_guest_visible_invoice ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_400_for_get_file_with_no_filename ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_404_for_get_file_with_a_malformed_url_key_prefix ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_404_for_get_file_whose_url_key_is_not_guest_visible ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_404_for_get_file_whose_url_key_belongs_to_a_draft_invoice ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_404_for_a_visible_invoice_whose_file_does_not_exist_on_disk ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_downloads_an_existing_file_for_a_guest_visible_invoice ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_path_traversal_attempt_in_the_filename ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_serves_attachment_route_the_same_as_get_file ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Invoices/InvoiceTaxRateServiceTest.php

#### it_returns_a_successful_response_or_redirect ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_expose_php_errors ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_a_guest_to_login ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Core/CustomValuesControllerTest.php

#### it_lists_every_value_for_a_field ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_creates_a_value_for_a_field ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_create_a_value_without_custom_values_value ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_updates_a_value ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 5

#### it_fails_to_update_a_value_without_custom_values_value ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_deletes_a_value ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 5

#### it_still_deletes_a_value_when_csrf_protection_is_on_and_the_token_is_valid ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_delete_a_value_when_the_csrf_token_is_missing ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_delete_a_value_on_a_plain_get_request ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_a_guest_to_login_and_leaks_no_value ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Core/DashboardControllerTest.php

#### it_displays_dashboard_with_a_200_status ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_renders_a_full_html_document_on_the_dashboard ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_includes_navigation_elements_on_the_dashboard ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_a_guest_away_from_the_dashboard ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_expose_php_errors_on_the_dashboard ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_produces_a_deterministic_dashboard_response_on_two_consecutive_requests ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: E
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_display_invoice_form_content_on_the_dashboard ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_includes_the_clients_section_link_on_the_dashboard ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_200_with_seeded_invoices_and_clients ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_200_with_multiple_seeded_clients ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Core/ViewTemplateSystemTest.php

#### it_returns_a_successful_response_or_redirect ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_a_guest_to_login ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Core/IntegrationTestConnectionTest.php

#### it_reports_a_reachable_provider_as_a_successful_connection ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_reports_the_provider_as_unreachable_when_authentication_fails ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_answers_with_a_json_body_carrying_the_three_probe_keys ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_non_post_request ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_errors_for_an_unknown_merchant_client ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_a_guest_away_from_the_test_connection_endpoint ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: E
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_shows_a_test_connection_control_on_the_provider_edit_form ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Core/SettingsControllerTest.php

#### it_renders_the_settings_page_with_a_stored_value ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_persists_a_changed_setting ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_removes_the_invoice_logo ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_removes_the_login_logo ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_ignores_an_unknown_logo_type ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_remove_a_logo_when_the_csrf_token_is_missing ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_warns_admins_when_setup_security_flags_are_not_enabled ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_warns_when_a_saved_custom_invoice_template_is_missing_from_ipconfig ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_warn_when_a_saved_custom_invoice_template_is_allowlisted_in_ipconfig ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_a_guest_away_from_settings ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Core/EmailTemplatesAjaxControllerTest.php

#### it_returns_a_successful_response_or_redirect ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Core/SuperPdpFlowTest.php

#### it_includes_superpdp_in_the_provider_registry ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_shows_a_superpdp_integration_on_the_settings_page ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 5

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_renders_the_superpdp_settings_edit_form ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 5

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_a_guest_away_from_the_superpdp_edit_form ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_persists_superpdp_credentials_to_the_database ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: C, E
- **Assertions**: 6

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_disables_all_other_providers_when_superpdp_is_enabled ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_private_ip_as_api_base_url_and_stays_on_the_edit_form ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_non_https_token_url_and_stays_on_the_edit_form ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_an_absolute_url_in_an_endpoint_path_field ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_shows_a_sent_superpdp_invoice_in_the_history_page ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 5

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_shows_an_empty_history_for_an_invoice_that_was_never_sent ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 5

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_shows_multiple_superpdp_responses_for_a_single_invoice ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_shows_a_rejected_status_in_the_invoice_history ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_a_guest_away_from_the_superpdp_history_page ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_an_error_when_send_invoice_references_an_unknown_merchant_client ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_an_error_when_send_invoice_uses_a_disabled_merchant_client ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_an_error_when_send_invoice_references_an_unknown_invoice ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_records_the_superpdp_external_id_in_the_merchant_response_table ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_records_a_failed_send_attempt_in_the_merchant_response_table ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Core/MailerControllerTest.php

#### it_renders_the_send_invoice_form_for_a_seeded_invoice ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_prefills_the_from_address_with_the_smtp_mail_from_setting ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_falls_back_to_the_current_user_email_when_smtp_mail_from_is_empty ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_a_guest_away_from_the_mailer ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Core/UsersControllerTest.php

#### it_lists_every_user ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_creates_a_user ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_create_without_user_email ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_create_without_user_name ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_create_without_user_password ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_mismatched_password_confirmation_on_create ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_renders_the_edit_form_for_the_requested_user_only ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_updates_a_user_without_touching_the_password ✓ HONEST
- **Score**: 66.7% (4/6 categories)
- **Categories Used**: A, B, D, E
- **Assertions**: 7

#### it_fails_to_update_without_user_email ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_update_without_user_name ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_deletes_a_secondary_user ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 5

#### it_never_deletes_the_primary_admin ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_still_deletes_a_user_when_csrf_protection_is_on_and_the_token_is_valid ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_delete_a_user_when_the_csrf_token_is_missing ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_delete_a_user_on_a_plain_get_request ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_duplicate_user_email_on_create ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 5

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_prevents_a_non_primary_admin_from_changing_another_users_password ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: E
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_a_guest_to_login_and_leaks_no_user ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Core/SetupCliControllerTest.php

#### it_denies_http_access_to_the_cli_controller ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_creates_a_default_admin_user_when_none_exist ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_skips_creating_a_default_admin_user_when_one_already_exists ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Core/ReportsControllerTest.php

#### it_generates_an_invoices_per_client_report_for_a_date_range_without_mutating_data ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_generates_a_sales_by_client_report ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_generates_a_payment_history_report ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_generates_an_invoice_aging_report ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_generates_a_sales_by_year_report ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_a_guest_to_login_and_serves_no_report ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Core/WelcomeControllerTest.php

#### it_displays_welcome_page ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Core/LoginSecurityTest.php

#### it_redirects_after_a_login_attempt_with_an_unknown_email ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_after_a_login_attempt_with_a_wrong_password ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_reveal_whether_an_email_exists_in_error_responses ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: E
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_expose_dashboard_content_after_a_failed_login ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_denies_login_for_an_inactive_user_even_with_the_correct_password ✓ HONEST
- **Score**: 66.7% (4/6 categories)
- **Categories Used**: A, B, D, E
- **Assertions**: 6

#### it_allows_login_for_an_active_user_with_the_correct_password ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_blocks_login_attempts_after_exceeding_the_ip_rate_limit ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_allows_login_when_previous_attempts_have_expired_from_the_window ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Core/FilterAjaxControllerTest.php

#### it_filters_invoices_by_query ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_expose_php_errors_when_filtering_invoices_without_a_query ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_treats_filter_invoices_query_as_a_literal_search_term ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_filters_quotes_by_query ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_filters_clients_by_query ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_filters_custom_fields_by_query ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_filters_custom_values_by_query ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_filters_custom_values_field_by_query ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_filters_projects_by_query ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_filters_tasks_by_query ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_filters_products_by_query ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_filters_users_by_query ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_filters_families_by_query ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_filters_recurring_invoices_by_query ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_filters_online_logs_by_query ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_filters_archives_by_query ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_filters_payments_by_query ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_requires_an_ajax_request ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Core/EmailTemplatesAjaxGetContentTest.php

#### it_gets_the_content_of_an_existing_template ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_null_for_an_unknown_template_id ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_requires_an_ajax_request ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Core/DashboardFeatureTest.php

#### it_renders_the_dashboard_with_a_200_status_when_authenticated ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_renders_a_full_html_document_on_the_dashboard ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_includes_navigation_elements_on_the_dashboard ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_a_guest_away_from_the_dashboard ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_expose_php_errors_on_the_dashboard ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_produces_a_deterministic_dashboard_response_on_two_consecutive_requests ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: E
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Core/QontoEInvoiceGenerationTest.php

#### it_generates_a_facturx_hybrid_pdf_and_transmits_it_to_qonto ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_transmit_when_the_seller_has_no_siren ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_transmit_when_the_invoice_currency_is_missing ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Core/SettingsAjaxAndVersionsTest.php

#### it_generates_a_16_character_hex_cron_key ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_generates_a_different_cron_key_on_each_call ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_requires_an_ajax_request_for_get_cron_key ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_lists_applied_versions ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_denies_versions_access_to_a_guest ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Core/CustomFieldsControllerTest.php

#### it_lists_every_custom_field ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_creates_a_custom_field ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 5

#### it_fails_to_create_without_custom_field_table ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_create_without_custom_field_label ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_create_without_custom_field_type ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_custom_field_table_that_is_not_allow_listed ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_renders_the_edit_form_for_the_requested_custom_field_only ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_updates_a_custom_field ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 5

#### it_fails_to_update_without_custom_field_label ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_update_without_custom_field_type ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_deletes_a_custom_field ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 5

#### it_still_deletes_a_custom_field_when_csrf_protection_is_on_and_the_token_is_valid ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_delete_a_custom_field_when_the_csrf_token_is_missing ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_a_guest_to_login_and_leaks_no_custom_field ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Core/PasswordResetTokenExpiryTest.php

#### it_rejects_a_password_change_when_the_reset_token_has_expired ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: E
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_clears_the_expired_token_after_a_rejected_password_change ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_allows_a_password_change_with_a_valid_unexpired_token ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_allows_a_password_change_when_no_expiry_is_stored ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_the_reset_link_when_the_token_has_expired ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Core/UploadControllerTest.php

#### it_returns_a_successful_response_or_redirect ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_a_guest_to_login ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Core/CsrfMutationParityTest.php

#### it_deletes_an_import_batch_with_a_valid_csrf_token ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_delete_an_import_batch_without_a_csrf_token ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_unassigns_a_client_from_a_user_with_a_valid_csrf_token ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_unassign_a_client_from_a_user_without_a_csrf_token ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, D, E
- **Assertions**: 4

#### it_deletes_a_payment_with_a_valid_csrf_token ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_delete_a_payment_without_a_csrf_token ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_recalculates_invoice_amounts_with_a_valid_csrf_token ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 4

#### it_does_not_recalculate_invoice_amounts_without_a_csrf_token ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_recalculates_quote_amounts_with_a_valid_csrf_token ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 4

#### it_does_not_recalculate_quote_amounts_without_a_csrf_token ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_changes_a_password_via_reset_with_a_valid_csrf_token ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_change_a_password_via_reset_without_a_csrf_token ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: E
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Core/EmailTemplatesControllerTest.php

#### it_lists_every_email_template ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_creates_an_email_template ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 5

#### it_fails_to_create_without_email_template_title ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_renders_the_edit_form_for_the_requested_email_template_only ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_updates_an_email_template ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 5

#### it_fails_to_update_without_email_template_title ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_deletes_an_email_template ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 5

#### it_still_deletes_an_email_template_when_csrf_protection_is_on_and_the_token_is_valid ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_delete_an_email_template_when_the_csrf_token_is_missing ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_a_guest_to_login_and_leaks_no_email_template ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Core/LetsPeppolFlowTest.php

#### it_includes_letspeppol_in_the_provider_registry ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_shows_a_letspeppol_integration_on_the_settings_page ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 5

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_renders_the_letspeppol_settings_edit_form ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 5

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_a_guest_away_from_the_letspeppol_edit_form ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_persists_letspeppol_credentials_to_the_database ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: C, E
- **Assertions**: 6

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_disables_all_other_providers_when_letspeppol_is_enabled ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_private_ip_as_api_base_url_and_stays_on_the_edit_form ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_non_https_token_url_and_stays_on_the_edit_form ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_an_absolute_url_in_an_endpoint_path_field ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_shows_a_sent_letspeppol_invoice_in_the_history_page ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 5

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_shows_an_empty_history_for_an_invoice_that_was_never_sent ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 5

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_shows_multiple_peppol_responses_for_a_single_invoice ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_shows_a_rejected_status_in_the_invoice_history ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_a_guest_away_from_the_letspeppol_history_page ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_an_error_when_send_invoice_references_an_unknown_merchant_client ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_an_error_when_send_invoice_uses_a_disabled_merchant_client ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_an_error_when_send_invoice_references_an_unknown_invoice ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_records_the_peppol_external_id_in_the_merchant_response_table ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_records_a_failed_send_attempt_in_the_merchant_response_table ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Core/ControllersAuthGuardTest.php

#### it_redirects_an_unauthenticated_visitor_away_from_admin_module ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_expose_php_errors_on_an_unauthenticated_request_to_admin_route ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Core/LetsPeppolInvoiceTransmissionTest.php

#### it_authenticates_then_transmits_and_logs_the_external_reference ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_records_a_failure_when_the_provider_rejects_the_document ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_records_a_failure_when_oauth_authentication_fails ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Core/QontoFlowTest.php

#### it_includes_qonto_in_the_provider_registry ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_shows_a_qonto_integration_on_the_settings_page ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 5

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_renders_the_qonto_settings_edit_form ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 5

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_a_guest_away_from_the_qonto_edit_form ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_persists_qonto_credentials_to_the_database ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: C, E
- **Assertions**: 8

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_disables_all_other_providers_when_qonto_is_enabled ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_private_ip_as_api_base_url_and_stays_on_the_edit_form ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_non_https_api_base_url_and_stays_on_the_edit_form ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_an_absolute_url_in_an_endpoint_path_field ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_shows_a_sent_qonto_invoice_in_the_history_page ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 5

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_shows_an_empty_history_for_an_invoice_that_was_never_sent ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 5

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_shows_multiple_qonto_responses_for_a_single_invoice ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_shows_a_rejected_status_in_the_invoice_history ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_a_guest_away_from_the_qonto_history_page ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_an_error_when_send_invoice_references_an_unknown_merchant_client ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_an_error_when_send_invoice_uses_a_disabled_merchant_client ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_an_error_when_send_invoice_references_an_unknown_invoice ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_records_the_qonto_external_id_in_the_merchant_response_table ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_records_a_failed_send_attempt_in_the_merchant_response_table ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Core/TaxRatesControllerTest.php

#### it_lists_every_tax_rate ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_creates_a_tax_rate ✓ HONEST
- **Score**: 66.7% (4/6 categories)
- **Categories Used**: A, B, D, E
- **Assertions**: 6

#### it_fails_to_create_without_tax_rate_name ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 5

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_create_without_tax_rate_percent ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 5

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_renders_the_edit_form_for_the_requested_tax_rate_only ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_updates_a_tax_rate ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 5

#### it_fails_to_update_without_tax_rate_name ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_fails_to_update_without_tax_rate_percent ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_deletes_a_tax_rate ✓ HONEST
- **Score**: 50.0% (3/6 categories)
- **Categories Used**: A, B, D
- **Assertions**: 5

#### it_still_deletes_a_tax_rate_when_csrf_protection_is_on_and_the_token_is_valid ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_delete_a_tax_rate_when_the_csrf_token_is_missing ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_a_guest_to_login_and_leaks_no_tax_rate ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Core/CoreAjaxControllerTest.php

#### it_returns_a_successful_response_or_redirect ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_a_guest_to_login ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Core/LayoutControllerTest.php

#### it_returns_a_successful_response_or_redirect ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_a_guest_to_login ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Core/ImportControllerTest.php

#### it_returns_a_successful_response_or_redirect ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_a_guest_to_login ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_lists_only_allowed_import_files ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_ignores_unapproved_import_filenames_on_submit ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Core/VersionsControllerTest.php

#### it_redirects_a_guest_to_login ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Core/QontoInvoiceTransmissionTest.php

#### it_imports_then_sends_by_einvoice_and_logs_the_client_invoice_id ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_records_a_failure_when_the_import_returns_no_client_invoice_id ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_records_the_import_error_without_attempting_send_by_einvoice ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Core/IntegrationsControllerTest.php

#### it_shows_a_configured_provider_on_the_settings_page ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_lists_the_known_providers_as_json ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_shows_an_enabled_provider_on_the_events_page ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_shows_an_enabled_provider_on_the_incoming_page ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_shows_the_outbound_transmission_history_for_an_invoice ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_a_guest_away_from_every_integrations_route ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Core/SetupControllerTest.php

#### it_allows_the_setup_flow_when_setup_is_explicitly_unlocked ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_locks_every_http_setup_route_after_setup_is_completed ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_direct_setup_steps_to_the_wizard_when_setup_is_unlocked ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Core/MailerAjaxControllerTest.php

#### it_shows_the_not_configured_view_for_invoice ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_shows_the_not_configured_view_for_quote ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_on_cancel_for_send_invoice_even_when_unconfigured ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_send_or_mark_an_invoice_sent_when_mailer_is_not_configured ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_on_cancel_for_send_quote_even_when_unconfigured ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_send_or_mark_a_quote_sent_when_mailer_is_not_configured ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Core/SuperPdpInvoiceTransmissionTest.php

#### it_authenticates_then_uploads_the_pdf_and_logs_the_external_reference ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_records_a_failure_when_the_provider_rejects_the_upload ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, D
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_records_a_failure_when_oauth_authentication_fails ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Core/SessionsSecurityTest.php

#### it_allows_a_referer_from_the_same_base_url ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_referer_from_an_external_domain ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_returns_the_safe_default_when_referer_is_empty ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_referer_that_starts_with_a_double_slash ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_accepts_an_alphanumeric_password_reset_token ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_accepts_a_hex_token_of_typical_length ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_token_containing_a_path_traversal_sequence ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_token_containing_a_slash ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_token_containing_special_characters ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_considers_an_expired_token_as_expired ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_considers_a_future_token_as_not_expired ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_enforces_the_max_expiry_minutes_cap_of_1440 ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_allows_a_valid_expiry_minutes_value_within_range ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_zero_expiry_minutes_and_falls_back_to_default ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_detects_curl_as_a_bot_user_agent ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_detects_python_requests_as_a_bot_user_agent ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_detects_an_empty_user_agent_as_a_bot ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_flag_a_normal_browser_user_agent_as_a_bot ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_removes_attempts_outside_the_rate_limit_time_window ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 0

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_considers_the_ip_rate_limited_when_attempt_count_meets_the_threshold ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_rate_limit_when_attempt_count_is_below_the_threshold ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_accepts_only_canonical_password_reset_expiry_strings ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Core/SessionsFeatureTest.php

#### it_renders_the_login_page_with_a_200_status_when_unauthenticated ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_includes_a_login_form_on_the_sessions_login_page ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_render_the_admin_dashboard_when_unauthenticated ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_to_login_when_post_credentials_are_missing ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_to_login_with_wrong_credentials ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_renders_the_password_reset_form_with_a_200_status ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_to_login_when_a_nonexistent_email_is_submitted_to_password_reset ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_reveal_whether_the_email_exists_in_the_reset_response ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: E
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_password_reset_token_containing_non_alphanumeric_characters ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: C
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_to_login_when_an_unknown_valid_format_token_is_used ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_destroys_the_session_and_redirects_to_login_on_logout ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_expose_php_errors_on_the_login_page ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Core/SendInvoiceGuardTest.php

#### it_rejects_a_send_to_a_provider_that_does_not_support_the_invoice_profile ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_does_not_transmit_on_a_plain_get_request ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_redirects_a_guest_away_from_the_send_endpoint ⚠️ HOLLOW
- **Score**: 33.3% (2/6 categories)
- **Categories Used**: A, B
- **Assertions**: 3

**⚠️ Improvement Suggestions:**

- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Setup/SpecialCharacterDatabasePasswordTest.php

#### it_accepts_passwords_with_special_characters_in_validation ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_rejects_a_password_containing_a_single_quote ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_prevents_newline_injection_in_passwords ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_prevents_null_byte_injection_in_passwords ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 2

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

#### it_round_trips_special_characters_through_the_config_file_unescaped ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 1

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Setup/EInvoiceResponsesFoldMigrationTest.php

#### it_folds_legacy_rows_into_ip_merchant_responses_before_dropping_the_table ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: F
- **Assertions**: 9

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);

#### it_is_safe_to_run_a_second_time ⚠️ HOLLOW
- **Score**: 0.0% (0/6 categories)
- **Categories Used**: None
- **Assertions**: 4

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);
- **Add F (Category 5)**: Add boundary tests: nonexistent ID (99999), invalid ID (non-numeric), ID=0, null values

### tests/Feature/Setup/PaymentExternalIdDedupMigrationTest.php

#### it_dedupes_existing_references_and_makes_the_index_unique_without_dropping_rows ⚠️ HOLLOW
- **Score**: 16.7% (1/6 categories)
- **Categories Used**: F
- **Assertions**: 5

**⚠️ Improvement Suggestions:**

- **Add A (Category 0)**: Add assertDatabaseHas() or assertSame() to verify state changed correctly
- **Add B (Category 1)**: Add assertDatabaseMissing() or count checks to verify no side effects
- **Add C (Category 2)**: Add assertResponseStatusCode() or assertResponseBodyContains()
- **Add D (Category 3)**: Add assertDatabaseRow() to verify relationships/foreign keys intact
- **Add E (Category 4)**: Test repeated request: $response2 = $this->post(...); assertResponseStatusCode($response2, 404);

## Interpretation

**Score < 50%**: Test is **hollow** — checks response properties only, doesn't verify business logic.

**Score ≥ 50%**: Test is **honest** — touches 3+ assertion categories, verifies logic + state.

### Categories:
- **A**: Business Logic (database state, computed values)
- **B**: State Isolation (side effects prevented, counts unchanged)
- **C**: Error Semantics (HTTP status, message content)
- **D**: Data Integrity (relationships, foreign keys)
- **E**: Idempotency (repeated requests safe)
- **F**: Boundary Cases (0, negative, nonexistent IDs, null)
