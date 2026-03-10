<?php

return [
    'base_url'     => env('OPENMRS_BASE_URL', 'http://localhost:8080/openmrs'),
    'username'     => env('OPENMRS_USERNAME', 'admin'),
    'password'     => env('OPENMRS_PASSWORD'),
    'concept_uuid' => env('OPENMRS_FOLLOWUP_CONCEPT_UUID', 'placeholder-uuid'),
];