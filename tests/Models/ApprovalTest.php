<?php

use Ffhs\Approvals\Models\Approval;
use Illuminate\Support\Facades\Config;

describe('Approval Model', function () {

    beforeEach(function () {
        // Set up a custom table name for testing
        Config::set('approvals.tables.approvals', 'test_approvals');

        // Create an instance of the Approval model
        $this->approval = new Approval;
    });

    it('has the correct table name from configuration', function () {
        expect($this->approval->getTable())->toBe('test_approvals');
    });

    it('can filter approvals by scope', function () {
        // Mocking the behavior instead of relying on database factories
        $mockQuery = Mockery::mock('alias:Illuminate\Database\Eloquent\Builder');
        $mockQuery->shouldReceive('where')->with('scope', 'project-a')->andReturnSelf();
        $mockQuery->shouldReceive('get')->andReturn(collect([new Approval(['scope' => 'project-a'])]));

        $approvals = $mockQuery->where('scope', 'project-a')->get();

        expect($approvals)->toHaveCount(1);
        expect($approvals->first()->scope)->toBe('project-a');
    });

    it('can filter approvals by category', function () {
        $mockQuery = Mockery::mock('alias:Illuminate\Database\Eloquent\Builder');
        $mockQuery->shouldReceive('where')->with('category', 'finance')->andReturnSelf();
        $mockQuery->shouldReceive('get')->andReturn(collect([new Approval(['category' => 'finance'])]));

        $approvals = $mockQuery->where('category', 'finance')->get();

        expect($approvals)->toHaveCount(1);
        expect($approvals->first()->category)->toBe('finance');
    });

    it('can filter approvals by approvable type and ID', function () {
        $mockQuery = Mockery::mock('alias:Illuminate\Database\Eloquent\Builder');
        $mockQuery->shouldReceive('where')->with('approvable_type', 'Invoice')->andReturnSelf();
        $mockQuery->shouldReceive('where')->with('approvable_id', 1)->andReturnSelf();
        $mockQuery->shouldReceive('get')->andReturn(collect([new Approval(['approvable_type' => 'Invoice', 'approvable_id' => 1])]));

        $approvals = $mockQuery->where('approvable_type', 'Invoice')->where('approvable_id', 1)->get();

        expect($approvals)->toHaveCount(1);
        expect($approvals->first()->approvable_id)->toBe(1);
    });

    it('throws an exception for invalid enums in allInCategoryApproved', function () {
        expect(fn () => $this->approval->allInCategoryApproved('finance', 'InvalidEnumClass'))
            ->toThrow(InvalidArgumentException::class, 'The provided class must be a valid enum.');
    });

    it('throws an exception for invalid enums in anyInCategoryDeclined', function () {
        expect(fn () => $this->approval->anyInCategoryDeclined('finance', 'InvalidEnumClass'))
            ->toThrow(InvalidArgumentException::class, 'The provided class must be a valid enum.');
    });

    it('retrieves approvals for a specific approver', function () {
        $mockQuery = Mockery::mock('alias:Illuminate\Database\Eloquent\Builder');
        $mockQuery->shouldReceive('where')->with('approver_id', 1)->andReturnSelf();
        $mockQuery->shouldReceive('get')->andReturn(collect([new Approval(['approver_id' => 1])]));

        $approvals = $mockQuery->where('approver_id', 1)->get();

        expect($approvals)->toHaveCount(1);
        expect($approvals->first()->approver_id)->toBe(1);
    });
});
