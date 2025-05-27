<?php
// Simple test to see what __METHOD__ returns in DBORM context

class TestClass {
    public function testMethod() {
        return __METHOD__;
    }
    
    public function callRunGetQuery() {
        // Simulate what _runGetQuery receives
        $getMethod = __METHOD__;
        echo "getMethod value: '" . $getMethod . "'\n";
        
        // Test the comparison
        if ($getMethod === 'TestClass::callRunGetQuery') {
            echo "Direct class match: YES\n";
        } else {
            echo "Direct class match: NO\n";
        }
        
        if ($getMethod === __CLASS__ . '::callRunGetQuery') {
            echo "Class constant match: YES\n";
        } else {
            echo "Class constant match: NO\n";
        }
    }
}

$test = new TestClass();
echo "Method name: " . $test->testMethod() . "\n";
$test->callRunGetQuery();
