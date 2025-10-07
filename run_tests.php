<?php

/**
 * Comprehensive Test Runner for MCC News Aggregator
 * 
 * This script runs all tests and provides detailed reporting
 */

require_once 'vendor/autoload.php';

use PHPUnit\Framework\TestSuite;
use PHPUnit\TextUI\TestRunner;
use PHPUnit\Framework\TestResult;

class MCCNewsAggregatorTestRunner
{
    private $testResults = [];
    private $totalTests = 0;
    private $passedTests = 0;
    private $failedTests = 0;
    private $skippedTests = 0;

    public function runAllTests()
    {
        echo "🚀 Starting Comprehensive Test Suite for MCC News Aggregator\n";
        echo "=" . str_repeat("=", 60) . "\n\n";

        $testSuites = [
            'Authentication Tests' => 'tests/Feature/AuthenticationTest.php',
            'Content Management Tests' => 'tests/Feature/ContentManagementTest.php',
            'User Dashboard Tests' => 'tests/Feature/UserDashboardTest.php',
            'API Tests' => 'tests/Feature/ApiTest.php',
            'Security Tests' => 'tests/Feature/SecurityTest.php',
            'Integration Tests' => 'tests/Feature/IntegrationTest.php',
            'User Dashboard Events Tests' => 'tests/Feature/UserDashboardEventsTest.php',
            'Event Status Tests' => 'tests/Feature/EventStatus24HourTest.php',
            'Unit Tests' => 'tests/Unit/EventStatusTest.php',
            'Example Tests' => 'tests/Feature/ExampleTest.php'
        ];

        foreach ($testSuites as $suiteName => $testFile) {
            if (file_exists($testFile)) {
                $this->runTestSuite($suiteName, $testFile);
            } else {
                echo "⚠️  Test file not found: {$testFile}\n";
            }
        }

        $this->generateReport();
    }

    private function runTestSuite($suiteName, $testFile)
    {
        echo "🧪 Running {$suiteName}...\n";
        echo str_repeat("-", 40) . "\n";

        try {
            // Run PHPUnit for the specific test file
            $command = "vendor/bin/phpunit {$testFile} --verbose --colors=never";
            $output = shell_exec($command . ' 2>&1');
            
            if ($output) {
                $this->parseTestOutput($output, $suiteName);
                echo $output . "\n";
            } else {
                echo "❌ Failed to run tests for {$suiteName}\n";
            }
        } catch (Exception $e) {
            echo "❌ Error running {$suiteName}: " . $e->getMessage() . "\n";
        }

        echo "\n";
    }

    private function parseTestOutput($output, $suiteName)
    {
        // Parse PHPUnit output to extract test results
        if (preg_match('/(\d+) tests?/', $output, $matches)) {
            $this->totalTests += (int)$matches[1];
        }
        
        if (preg_match('/(\d+) assertions?/', $output, $matches)) {
            // This is a rough estimate
        }
        
        if (strpos($output, 'FAILURES!') !== false) {
            $this->failedTests++;
        } elseif (strpos($output, 'OK') !== false) {
            $this->passedTests++;
        }
    }

    private function generateReport()
    {
        echo "📊 TEST EXECUTION REPORT\n";
        echo "=" . str_repeat("=", 60) . "\n";
        echo "Total Tests: {$this->totalTests}\n";
        echo "Passed: {$this->passedTests}\n";
        echo "Failed: {$this->failedTests}\n";
        echo "Skipped: {$this->skippedTests}\n\n";

        if ($this->failedTests > 0) {
            echo "❌ Some tests failed. Please review the output above.\n";
        } else {
            echo "✅ All tests passed successfully!\n";
        }

        echo "\n🔍 Test Coverage Areas:\n";
        echo "- Authentication & Authorization\n";
        echo "- Content Management (Announcements, Events, News)\n";
        echo "- User Dashboard & Interactions\n";
        echo "- API Endpoints & Responses\n";
        echo "- Security & Validation\n";
        echo "- Integration Workflows\n";
        echo "- Department Visibility\n";
        echo "- Comment System\n";
        echo "- Notification System\n";
        echo "- Chatbot Integration\n";
        echo "- File Upload & Media Handling\n";
        echo "- Password Reset Flow\n";
        echo "- User Registration Flow\n\n";

        echo "🎯 Next Steps:\n";
        echo "1. Review any failed tests and fix issues\n";
        echo "2. Run tests in CI/CD pipeline\n";
        echo "3. Add more edge case tests as needed\n";
        echo "4. Monitor test performance and optimize\n";
        echo "5. Consider adding browser tests for UI\n\n";
    }

    public function runSpecificTest($testFile)
    {
        if (!file_exists($testFile)) {
            echo "❌ Test file not found: {$testFile}\n";
            return;
        }

        echo "🧪 Running specific test: {$testFile}\n";
        echo str_repeat("-", 40) . "\n";

        $command = "vendor/bin/phpunit {$testFile} --verbose --colors=always";
        $output = shell_exec($command . ' 2>&1');
        
        if ($output) {
            echo $output . "\n";
        } else {
            echo "❌ Failed to run test\n";
        }
    }

    public function runTestsWithCoverage()
    {
        echo "🧪 Running tests with coverage report...\n";
        echo str_repeat("-", 40) . "\n";

        $command = "vendor/bin/phpunit --coverage-html coverage --coverage-text tests/";
        $output = shell_exec($command . ' 2>&1');
        
        if ($output) {
            echo $output . "\n";
            echo "📊 Coverage report generated in 'coverage' directory\n";
        } else {
            echo "❌ Failed to run tests with coverage\n";
        }
    }
}

// Command line interface
if (php_sapi_name() === 'cli') {
    $runner = new MCCNewsAggregatorTestRunner();
    
    $args = $argv ?? [];
    
    if (count($args) > 1) {
        $command = $args[1];
        
        switch ($command) {
            case 'all':
                $runner->runAllTests();
                break;
            case 'coverage':
                $runner->runTestsWithCoverage();
                break;
            case 'specific':
                if (isset($args[2])) {
                    $runner->runSpecificTest($args[2]);
                } else {
                    echo "Usage: php run_tests.php specific <test_file>\n";
                }
                break;
            default:
                echo "Usage: php run_tests.php [all|coverage|specific <test_file>]\n";
                echo "  all      - Run all tests\n";
                echo "  coverage - Run tests with coverage report\n";
                echo "  specific - Run specific test file\n";
                break;
        }
    } else {
        $runner->runAllTests();
    }
} else {
    echo "This script should be run from the command line.\n";
}
