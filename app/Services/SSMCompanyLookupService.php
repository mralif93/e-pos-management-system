<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class SSMCompanyLookupService
{
    protected string $apiKey;
    protected string $baseUrl;
    protected bool $isProduction;

    public function __construct()
    {
        $this->apiKey = config('services.ssm.api_key', '');
        $this->baseUrl = config('services.ssm.production', false) 
            ? 'https://www.ssm.com.my/API' 
            : 'https://test1.ssm.com.my/API';
        $this->isProduction = config('services.ssm.production', false);
    }

    public function searchByCompanyName(string $companyName, int $limit = 10): array
    {
        $cacheKey = 'ssm_company_search_' . md5($companyName);
        
        return Cache::remember($cacheKey, 3600, function () use ($companyName, $limit) {
            try {
                $response = Http::timeout(30)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $this->apiKey,
                        'Content-Type' => 'application/json',
                    ])
                    ->get("{$this->baseUrl}/company/search", [
                        'company_name' => $companyName,
                        'limit' => $limit,
                    ]);

                if ($response->successful()) {
                    return $this->formatSearchResults($response->json());
                }

                return [
                    'success' => false,
                    'error' => 'SSM API request failed',
                    'details' => $response->body(),
                ];
            } catch (\Exception $e) {
                return [
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
            }
        });
    }

    public function searchByRegistrationNumber(string $registrationNumber): array
    {
        $cacheKey = 'ssm_company_' . md5($registrationNumber);
        
        return Cache::remember($cacheKey, 86400, function () use ($registrationNumber) {
            try {
                $response = Http::timeout(30)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $this->apiKey,
                        'Content-Type' => 'application/json',
                    ])
                    ->get("{$this->baseUrl}/company/info", [
                        'registration_no' => $registrationNumber,
                    ]);

                if ($response->successful()) {
                    return $this->formatCompanyDetails($response->json());
                }

                return [
                    'success' => false,
                    'error' => 'Company not found or SSM API request failed',
                    'details' => $response->body(),
                ];
            } catch (\Exception $e) {
                return [
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
            }
        });
    }

    public function getCompanyOfficers(string $registrationNumber): array
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->get("{$this->baseUrl}/company/officers", [
                    'registration_no' => $registrationNumber,
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'officers' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to fetch company officers',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    protected function formatSearchResults(array $data): array
    {
        $companies = collect($data['companies'] ?? $data ?? [])
            ->map(function ($company) {
                return [
                    'registration_number' => $company['company_no'] ?? $company['registration_no'] ?? null,
                    'name' => $company['company_name'] ?? $company['name'] ?? null,
                    'status' => $company['company_status'] ?? $company['status'] ?? null,
                    'registration_date' => $company['registration_date'] ?? null,
                    'address' => $company['registered_address'] ?? $company['address'] ?? null,
                ];
            })
            ->toArray();

        return [
            'success' => true,
            'companies' => $companies,
            'total' => count($companies),
        ];
    }

    protected function formatCompanyDetails(array $data): array
    {
        return [
            'success' => true,
            'company' => [
                'registration_number' => $data['company_no'] ?? $data['registration_no'] ?? null,
                'name' => $data['company_name'] ?? $data['name'] ?? null,
                'former_name' => $data['former_name'] ?? null,
                'status' => $data['company_status'] ?? $data['status'] ?? null,
                'registration_date' => $data['registration_date'] ?? null,
                'incorporation_date' => $data['incorporation_date'] ?? null,
                'address' => $data['registered_address'] ?? null,
                'business_address' => $data['business_address'] ?? null,
                'phone' => $data['phone_no'] ?? $data['phone'] ?? null,
                'fax' => $data['fax_no'] ?? $data['fax'] ?? null,
                'email' => $data['email'] ?? null,
                'website' => $data['website'] ?? null,
                'ssm_id' => $data['ssm_id'] ?? null,
                'type' => $data['company_type'] ?? $data['type'] ?? null,
                'nature_of_business' => $data['nature_of_business'] ?? null,
            ],
        ];
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    public function getMockCompanyData(string $registrationNumber): array
    {
        return [
            'success' => true,
            'company' => [
                'registration_number' => $registrationNumber,
                'name' => 'SAMPLE COMPANY SDN BHD',
                'former_name' => null,
                'status' => 'ACTIVE',
                'registration_date' => '2015-01-15',
                'incorporation_date' => '2015-01-15',
                'address' => 'No. 123, Jalan Merchant, 50000 Kuala Lumpur, Malaysia',
                'business_address' => 'No. 123, Jalan Merchant, 50000 Kuala Lumpur, Malaysia',
                'phone' => '+603-12345678',
                'fax' => '+603-12345679',
                'email' => 'info@samplecompany.com',
                'website' => 'www.samplecompany.com',
                'ssm_id' => '1234567-A',
                'type' => 'Private Company',
                'nature_of_business' => 'Retail Trade',
            ],
        ];
    }
}
