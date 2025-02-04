<?php

namespace Ffhs\Approvals\Infolists\Actions;

use Ffhs\Approvals\Approval\ApprovalFlow;
use Ffhs\Approvals\ApprovalBy;
use Ffhs\Approvals\Traits\HasApprovalActionModifications;
use Ffhs\Approvals\Traits\HasApprovals;
use Filament\Infolists\Components\Component;
use Filament\Support\Concerns\HasAlignment;
use Filament\Support\Concerns\HasVerticalAlignment;
use Mockery\Matcher\Closure;
use Symfony\Component\ErrorHandler\Error\UndefinedFunctionError;

class ApprovalActions extends Component
{
    use HasAlignment;
    use HasVerticalAlignment;
    use HasApprovalActionModifications;


    protected bool | Closure $isFullWidth = false;
    protected string $view = 'filament-package_ffhs_approvals::infolist.approval-actions';

    protected string|Closure $approvalKey;
    protected ApprovalFlow|null $cachedApprovalFlow = null;



    final public function __construct(string|Closure $approvalKey)
    {
        $this->approvalKey($approvalKey);
    }


    public static function make(string|Closure $approvalKey): static
    {
        $static = app(static::class, ['approvalKey' => $approvalKey]);
        $static->configure();

        return $static;
    }

    public function approvalKey(string|Closure  $approvalKey):static
    {
        $this->approvalKey = $approvalKey;
        return $this;
    }

    public function getApprovalKey():string
    {
        return $this->evaluate($this->approvalKey);
    }


    public function getApprovalFlow(): ApprovalFlow{

        if(!is_null($this->cachedApprovalFlow))
            return $this->cachedApprovalFlow;


        /** @var HasApprovals $record */
        $record = $this->getRecord();
//        if(!($record instanceof HasApprovals))
//            throw new \RuntimeException('Record hasn\'t an Approval Flow');

        try {
            $this->cachedApprovalFlow = $record->getApprovalFlows()[$this->getApprovalKey()] ?? null;
        }catch (UndefinedFunctionError){
            throw new \RuntimeException('Record hasn\'t an Approval Flow [function getApprovalFlows()]');
        }


        if($this->cachedApprovalFlow === null)
            throw new \RuntimeException('Record hasn\'t an Approval Flow with key "'.$this->getApprovalKey().'"');

        return $this->cachedApprovalFlow;
    }

    public function getApprovalStatus(): array
    {
        return $this->getApprovalFlow()->getApprovalStatus();
    }

    public function getActions(): array
    {
        $allActions = [];
        foreach ($this->getApprovalFlow()->getApprovalBys() as $approvalBy) {
           $allActions = array_merge($allActions, $this->getApprovalByActions($approvalBy));
        }

        return $allActions;
    }





    public function getApprovalByActions(ApprovalBy $approvalBy):array
    {
        $labelMap = $this->getApprovalActionsLabel();
        $colorMap = $this->getApprovalActionsColor();

        $actions = [];
        foreach ($this->getApprovalStatus() as $status){
            $label = $labelMap[$status->value] ?? $status->value;
            $color = $colorMap[$status->value] ?? null;

            $actions[] = ApprovalAction::make($approvalBy->getName() . '-' . $status->value)
                ->color($color)
                ->label($label);
        }

        return $actions;
    }


    public function fullWidth(bool | Closure $isFullWidth = true): static
    {
        $this->isFullWidth = $isFullWidth;

        return $this;
    }

    public function isFullWidth(): bool
    {
        return (bool) $this->evaluate($this->isFullWidth);
    }








//    protected ?string $category = null;
//
//    protected ?ApprovalFlow $approvalFlow = null;
//
//    protected $statusClass = null;
//
//
//
//    public static function make(string|array $key): static
//    {
//        $actions = [];
//
//        foreach ($options as $option) {
//
//            if ($option instanceof HasApprovalStatuses && $option instanceof BackedEnum) {
//                $actions[] = ApprovalAction::make($option->value)
//                    ->status($option);
//
//                continue;
//            }
//
//            $actions[] = $option;
//        }
//
//        $static = app(static::class, ['actions' => $actions, 'statusClass' => $options[0]::class]);
//        $static->configure();
//
//        return $static;
//    }
//
//    public function category(string $category): static
//    {
//        $this->category = $category;
//
//        foreach ($this->childComponents as $actionContainer) {
//            $actionContainer->statePath($this->category);
//
//            $action = $actionContainer->action;
//
//            if ($action instanceof ApprovalAction) {
//                $action->category($this->category);
//            }
//        }
//
//        return $this;
//    }
//
//    public function statusCategoryColors(array $colors): static
//    {
//        foreach ($this->childComponents as $actionContainer) {
//            $action = $actionContainer->action;
//            if ($action instanceof ApprovalAction) {
//                $action->statusCategoryColors($colors);
//            }
//        }
//
//        return $this;
//    }
//
//    public function approvalFlow(ApprovalFlow $approvalFlow): static
//    {
//        $this->approvalFlow = $approvalFlow;
//
//        foreach ($this->childComponents as $actionContainer) {
//            $action = $actionContainer->action;
//            if ($action instanceof ApprovalAction) {
//                $action->approvalFlow($this->approvalFlow);
//                $action->disabled(
//                    fn ($record) => $this->approvalFlow->shouldDisable($record, $this->category, $action->getStatusEnumClass())
//                );
//                $action->visible(fn () => $this->approvalFlow->shouldBeVisible());
//
//            }
//        }
//
//        return $this;
//    }
}
