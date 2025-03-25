<?php

use App\Approvals\TestApprovalStatuses;
use Ffhs\Approvals\Approval\SimpleApprovalBy;
use Ffhs\Approvals\Approval\SimpleApprovalFlow;
use Ffhs\Approvals\Contracts\Approvable;
use Ffhs\Approvals\Contracts\Approver;
use Ffhs\Approvals\Enums\ApprovalState;
use Ffhs\Approvals\Models\Approval;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;


dataset('approved dataset', [
    [
        'result' => ApprovalState::DENIED,
        'atLeast' => null,
        'approvals' => [
            (new Approval)->fill([
                'key' => 'key',
                'approval_by' => 'approval_by',
                'status' => TestApprovalStatuses::DENIED->value,
            ]),
            (new Approval)->fill([
                'key' => 'key',
                'approval_by' => 'approval_by',
                'status' => TestApprovalStatuses::PENDING->value,
            ]),
            (new Approval)->fill([
                'key' => 'key',
                'approval_by' => 'approval_by',
                'status' => TestApprovalStatuses::APPROVED->value,
            ]),
        ],
    ],

    [
        'result' => ApprovalState::PENDING,
        'atLeast' => null,
        'approvals' => [
            (new Approval)->fill([
                'key' => 'key',
                'approval_by' => 'approval_by',
                'status' => TestApprovalStatuses::PENDING->value,
            ]),
            (new Approval)->fill([
                'key' => 'key',
                'approval_by' => 'approval_by',
                'status' => TestApprovalStatuses::PENDING->value,
            ]),
            (new Approval)->fill([
                'key' => 'key',
                'approval_by' => 'approval_by',
                'status' => TestApprovalStatuses::APPROVED->value,
            ]),
            (new Approval)->fill([
                'key' => 'key',
                'approval_by' => 'approval_by',
                'status' => TestApprovalStatuses::APPROVED->value,
            ]),
        ],
    ],

    [
        'result' => ApprovalState::OPEN,
        'atLeast' => null,
        'approvals' => [],
    ],

    [
        'result' => ApprovalState::OPEN,
        'atLeast' => 2,
        'approvals' => [
            (new Approval)->fill([
                'key' => 'key',
                'approval_by' => 'approval_by',
                'status' => TestApprovalStatuses::APPROVED->value,
            ]),
        ],
    ],

    [
        'result' => ApprovalState::OPEN,
        'atLeast' => 4,
        'approvals' => [
            (new Approval)->fill([
                'key' => 'key',
                'approval_by' => 'approval_by',
                'status' => TestApprovalStatuses::APPROVED->value,
            ]),
            (new Approval)->fill([
                'key' => 'key',
                'approval_by' => 'approval_by',
                'status' => TestApprovalStatuses::APPROVED->value,
            ]),
            (new Approval)->fill([
                'key' => 'key',
                'approval_by' => 'approval_by',
                'status' => TestApprovalStatuses::APPROVED->value,
            ]),
        ],
    ],
    [
        'result' => ApprovalState::APPROVED,
        'atLeast' => 2,
        'approvals' => [
            (new Approval)->fill([
                'key' => 'key',
                'approval_by' => 'approval_by',
                'status' => TestApprovalStatuses::APPROVED->value,
            ]),
            (new Approval)->fill([
                'key' => 'key',
                'approval_by' => 'approval_by',
                'status' => TestApprovalStatuses::APPROVED->value,
            ]),
        ],
    ],
]);
describe('SimpleApprovalBy test', function () {
    beforeEach(function () {
        // Set up a custom table name for testing
        Config::set('filament-package_ffhs_approvals.tables.approvals', 'test_approvals');

        // Create an instance of the Approval model
        $this->approval = new Approval();
    });

    describe('canApprove', function () {
        test('user can\'t approve by CanApproveUsing', function () {
            $user = Mockery::mock(\App\Models\User::class);
            $approvable = Mockery::mock(Approvable::class);
            $approvalBy = SimpleApprovalBy::make('approvalBy')
                ->canApproveUsing(fn($approver) => $approver !== $user);

            Gate::shouldReceive('allows')
                ->never()
                ->with('can_approve_by', $approvalBy);

            $result = $approvalBy->canApprove($user, $approvable);
            expect($result)->toBeFalse();
        });

        test('user can approve by CanApproveUsing', function () {
            $user = Mockery::mock(\App\Models\User::class);
            $approvable = Mockery::mock(Approvable::class);
            $approvalBy = SimpleApprovalBy::make('approvalBy')
                ->canApproveUsing(fn($approver) => $approver === $user);

            Gate::shouldReceive('allows')
                ->never()
                ->with('can_approve_by', $approvalBy);

            expect($approvalBy->canApprove($user, $approvable))->toBeTrue();
        });

        test('user can approve any', function () {
            $user = Mockery::mock(\App\Models\User::class);
            $approvable = Mockery::mock(Approvable::class);
            $approvalBy = SimpleApprovalBy::make('approvalBy')
                ->any();

            Gate::shouldReceive('allows')
                ->never()
                ->with('can_approve_by', $approvalBy);

            $result = $approvalBy->canApprove($user, $approvable);
            expect($result)->toBeTrue();
        });


        test('user can\'t approve by permission in gate', function () {
            $user = Mockery::mock(\App\Models\User::class);
            $approvable = Mockery::mock(Approvable::class);
            $approvalBy = SimpleApprovalBy::make('approvalBy')
                ->permission('not_existing');

            Gate::shouldReceive('allows')
                ->once()
                ->with('can_approve_by', $approvalBy)
                ->andReturn(false);

            $result = $approvalBy->canApprove($user, $approvable);
            expect($result)->toBeFalse();
        });

        test('user can approve by permission in gate', function () {
            $user = Mockery::mock(\App\Models\User::class);
            $approvable = Mockery::mock(Approvable::class);
            $approvalBy = SimpleApprovalBy::make('approvalBy')
                ->permission('existing');

            Gate::shouldReceive('allows')
                ->once()
                ->with('can_approve_by', $approvalBy)
                ->andReturn(true);

            expect($approvalBy->canApprove($user, $approvable))
                ->toBeTrue();
        });


        test('user can\'t approve by permission without gate', function () {
            $user = Mockery::mock(Approver::class);
            $user->shouldReceive('hasPermissionTo')
                ->andReturn(false);

            $approvable = Mockery::mock(Approvable::class);
            $approvalBy = SimpleApprovalBy::make('approvalBy')
                ->permission('not_existing');

            Gate::shouldReceive('allows')
                ->with('can_approve_by', $approvalBy)
                ->never();

            expect($approvalBy->canApprove($user, $approvable))
                ->toBeFalse();
        });

        test('user can approve by permission without gate', function () {
            $user = Mockery::mock(Model::class, Approver::class);
            $user->shouldReceive('hasPermissionTo')
                ->andReturn(true);
            $approvable = Mockery::mock(Approvable::class);
            $approvalBy = SimpleApprovalBy::make('approvalBy')
                ->permission('existing');

            Gate::shouldReceive('allows')
                ->with('can_approve_by', $approvalBy)
                ->never();

            expect($approvalBy->canApprove($user, $approvable))
                ->toBeTrue();
        });
    });

    describe('canApproveFromPermissions', function () {
        it('can\'t approve by failure ', function () {
            $user = Mockery::mock(\App\Models\User::class);
            $user->shouldReceive('hasPermissionTo')
                ->andReturn(fn($permisison) => throw new \Exception('failed'));
            $approvalBy = SimpleApprovalBy::make('approvalBy')
                ->permission('failing');

            expect($approvalBy->canApproveFromPermissions($user))
                ->toBeFalse();
        });

        it('can\'t approve by nothing ', function () {
            $user = Mockery::mock(\App\Models\User::class);
            $approvalBy = SimpleApprovalBy::make('approvalBy');

            expect($approvalBy->canApproveFromPermissions($user))
                ->toBeFalse();
        });

        it('can\'t approve by not by role ', function () {
            $user = Mockery::mock(\App\Models\User::class);
            $user
                ->shouldReceive('hasRole')
                ->andReturn(false);

            $approvalBy = SimpleApprovalBy::make('approvalBy')
                ->role('not_existing');

            expect($approvalBy->canApproveFromPermissions($user))
                ->toBeFalse();
        });

        it('can approve by not by role ', function () {
            $user = Mockery::mock(\App\Models\User::class);
            $user
                ->shouldReceive('hasRole')
                ->andReturn(true);

            $approvalBy = SimpleApprovalBy::make('approvalBy')
                ->role('existing');

            expect($approvalBy->canApproveFromPermissions($user))
                ->toBeTrue();
        });

        it('can\'t approve by not by permission', function () {
            $user = Mockery::mock(\App\Models\User::class);
            $user
                ->shouldReceive('hasPermissionTo')
                ->andReturn(false);

            $approvalBy = SimpleApprovalBy::make('approvalBy')
                ->permission('not_existing');

            expect($approvalBy->canApproveFromPermissions($user))
                ->toBeFalse();
        });

        it('can approve by not by permission', function () {
            $user = Mockery::mock(\App\Models\User::class);
            $user
                ->shouldReceive('hasPermissionTo')
                ->andReturn(true);

            $approvalBy = SimpleApprovalBy::make('approvalBy')
                ->permission('existing');

            expect($approvalBy->canApproveFromPermissions($user))
                ->toBeTrue();
        });
    });

    it('getApprovals', function () {
        $approvals = collect([
            Approval::make(['key' => 'key1', 'approval_by' => 'approval_by_1']),
            Approval::make(['key' => 'key1', 'approval_by' => 'approval_by_2']),
            Approval::make(['key' => 'key1', 'approval_by' => 'approval_by_3']),

            Approval::make(['key' => 'key2', 'approval_by' => 'approval_by_1']),
            Approval::make(['key' => 'key2', 'approval_by' => 'approval_by_2']),
            Approval::make(['key' => 'key2', 'approval_by' => 'approval_by_3']),

            Approval::make(['key' => 'key3', 'approval_by' => 'approval_by_1']),
            Approval::make(['key' => 'key3', 'approval_by' => 'approval_by_2']),
            Approval::make(['key' => 'key3', 'approval_by' => 'approval_by_2']),
        ]);

        $approvalBy = SimpleApprovalBy::make('approval_by_2');

        $approvable = Mockery::mock(Model::class, Approvable::class);
        $approvable
            ->shouldReceive('getAttribute')
            ->with('approvals')
            ->andReturn($approvals);

        $boundApprovals = $approvalBy->getApprovals($approvable, 'key3');
        expect($boundApprovals)
            ->toHaveCount(2);
        foreach ($boundApprovals as $boundApproval) {
            expect($boundApprovals->count())->toBe(2)
                ->and($boundApproval->key)->toBe('key3')
                ->and($boundApproval->approval_by)->toBe('approval_by_2');
        }
    });

    it('approved', function (ApprovalState $result, ?int $atLeast, array $approvals) {
        $approvalBy = SimpleApprovalBy::make('approval_by')->any();

        if (!is_null($atLeast)) {
            $approvalBy->atLeast($atLeast);
        }

        $approvable = Mockery::mock(Model::class, Approvable::class);

        $approvable
            ->shouldReceive('getAttribute')
            ->with('approvals')
            ->andReturn(collect($approvals));

        $approvable
            ->shouldReceive('getApprovalFlow')
            ->andReturn(
                SimpleApprovalFlow::make()
                    ->approvalStatus(TestApprovalStatuses::cases())
                    ->approvalBy([
                        $approvalBy,
                    ]),
            );

        expect($approvalBy->approved($approvable, 'key'))
            ->toBe($result);
    })->with('approved dataset');
});
