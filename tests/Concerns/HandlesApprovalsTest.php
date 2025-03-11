<?php

use Ffhs\Approvals\Concerns\HandlesApprovals;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;

describe('HandlesApprovals Trait', function () {
    beforeEach(function () {
        // Create a class that uses the HandlesApprovals trait

        $this->action = new class() {
            use HandlesApprovals;

            public string $name = 'approve';
            public MockInterface|null $mockRecord = null;
            public string $category;
            public mixed $status;

            public function getCategory(): string
            {
                return $this->category;
            }

            public function getRecord(): MockInterface|LegacyMockInterface|stdClass|null
            {
                $this->mockRecord = Mockery::mock(stdClass::class);
                $this->mockRecord->id = 1;

                return $this->mockRecord;
            }

            public function getStatus()
            {
                return $this->status;
            }
        };
    });

    it('sets the category', function () {
        $this
            ->action
            ->category('custom-category');

        expect($this->action->getCategory())->toBe('custom-category');
    });

    it('sets the status', function () {
        $mockStatus = Mockery::mock(HasApprovalStatuses::class);
        $this
            ->action
            ->status($mockStatus);

        expect($this->action->getStatus())
            ->toBe($mockStatus);
    });

    afterEach(function () {
        Mockery::close();
    });
});
