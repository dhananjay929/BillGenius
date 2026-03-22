<?php
/**
 * Smart Biller - Bill Controller
 * Handles loading DISCOM functions and dispatching bill fetch requests.
 */

class BillController
{
    private string $curlFunctionsPath;
    private array  $loadedDiscoms = [];

    public function __construct(string $curlFunctionsPath)
    {
        $this->curlFunctionsPath = rtrim($curlFunctionsPath, '/');
        $this->loadAllDiscoms();
    }

    /**
     * Auto-load every .php file in the curl_functions directory.
     */
    private function loadAllDiscoms(): void
    {
        foreach (glob($this->curlFunctionsPath . '/*.php') as $file) {
            require_once $file;
            $name = pathinfo($file, PATHINFO_FILENAME);
            $this->loadedDiscoms[] = $name;
        }
    }

    /**
     * Returns list of available DISCOMs that have a matching get_details_* function.
     */
    public function getAvailableDiscoms(): array
    {
        return array_filter($this->loadedDiscoms, function ($name) {
            return function_exists('get_details_' . $name);
        });
    }

    /**
     * Fetch a bill for the given consumer details.
     *
     * @param array $consumerDetails  Must include 'discom_name' key.
     * @return array{success: bool, message: string, file_path?: string}
     */
    public function fetchBill(array $consumerDetails): array
    {
        $discomName = trim($consumerDetails['discom_name'] ?? '');

        if (empty($discomName)) {
            return ['success' => false, 'message' => 'Please select a DISCOM.'];
        }

        $functionName = 'get_details_' . $discomName;

        if (!function_exists($functionName)) {
            return ['success' => false, 'message' => "Handler for '{$discomName}' not found."];
        }

        $result = $functionName($consumerDetails);

        if (!empty($result['file_path'])) {
            return [
                'success'   => true,
                'message'   => 'Bill fetched successfully!',
                'file_path' => ltrim($result['file_path'], './'),
                'bill_date'              => $result['bill_date']              ?? '',
                'due_date'               => $result['due_date']               ?? '',
                'amount_before_due_date' => $result['amount_before_due_date'] ?? '',
                'amount_after_due_date'  => $result['amount_after_due_date']  ?? '',
                'bill_no'                => $result['bill_no']                ?? '',
            ];
        }

        return [
            'success' => false,
            'message' => $result['message'] ?? 'No bill found for the given details.',
        ];
    }
}
