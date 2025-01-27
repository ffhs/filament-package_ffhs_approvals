<?php

use Ffhs\Approvals\Models\Approval;
use Illuminate\Support\Facades\Config;

describe('Approval Model', function () {
    beforeEach(function () {
        // Set up a custom table name for testing
        Config::set('filament-package_ffhs_approvals.tables.approvals', 'test_approvals');

        // Create an instance of the Approval model
        $this->approval = new Approval();
    });

    it('has the correct table name from configuration', function () {
        expect($this->approval->getTable())->toBe('test_approvals');
    });
});
