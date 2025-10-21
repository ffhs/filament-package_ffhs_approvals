<?php

namespace Ffhs\Approvals\Traits\Filament;

use Ffhs\Approvals\Contracts\Approvable;
use Ffhs\Approvals\Contracts\ApprovalFlow;
use RuntimeException;
use Symfony\Component\ErrorHandler\Error\UndefinedFunctionError;


/**
 * @method Approvable getRecord()
 */
trait HasApprovalFlowFromRecord
{
    private ApprovalFlow|null $cachedApprovalFlow = null;

    public function getApprovalStatuses(): array
    {
        return $this->getApprovalFlow()->getApprovalStatus();
    }

    public function getApprovalFlow(): ApprovalFlow
    {
        if (!is_null($this->cachedApprovalFlow)) {
            return $this->cachedApprovalFlow;
        }

        $record = $this->getRecord();

        /**  @phpstan-ignore-next-line */
        if (!$record instanceof Approvable) {
            throw new RuntimeException(
                'Record hasn\'t an Approval Flow becoause it is not approvable (It doesn\'t implements Approvable [' . $record::class . '])'
            );
        }

        try {
            $this->cachedApprovalFlow = $record->getApprovalFlow($this->getApprovalKey());
        } catch (UndefinedFunctionError) {
            throw new RuntimeException('Record hasn\'t an Approval Flow [function getApprovalFlows()]');
        }
//        catch (ErrorException) {
//            throw new RuntimeException('The key ' . $this->getApprovalKey() . ' doesnt exist');
//        }

        if ($this->cachedApprovalFlow === null) {
            throw new RuntimeException('Record hasn\'t an Approval Flow with key "' . $this->getApprovalKey() . '"');
        }

        return $this->cachedApprovalFlow;
    }

    abstract public function getApprovalKey(): string;
}
