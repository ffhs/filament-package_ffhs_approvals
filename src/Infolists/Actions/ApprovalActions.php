<?php

namespace Ffhs\Approvals\Infolists\Actions;

use BackedEnum;
use Ffhs\Approvals\ApprovalFlow;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;
use Ffhs\Approvals\Models\Approval;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action;

class ApprovalActions extends Actions
{
    protected ?string $category = null;

    protected ?string $scope = null;

    protected ?ApprovalFlow $approvalFlow = null;

    protected $statusClass = null;

    /**
     * @param  array<HasApprovalStatuses|Action>  $options
     */
    public static function make(array $options): static
    {
        $actions = [];

        foreach ($options as $option) {

            if ($option instanceof HasApprovalStatuses && $option instanceof BackedEnum) {
                $actions[] = ApprovalAction::make($option->value)
                    ->status($option);

                continue;
            }

            $actions[] = $option;
        }

        $static = app(static::class, ['actions' => $actions, 'statusClass' => $options[0]::class]);
        $static->configure();

        return $static;
    }

    public function category(string $category): static
    {
        $this->category = $category;

        foreach ($this->childComponents as $actionContainer) {
            $actionContainer->statePath($this->category);

            $action = $actionContainer->action;

            if ($action instanceof ApprovalAction) {
                $action->category($this->category);
            }
        }

        return $this;
    }

    public function scope(string $scope): static
    {
        $this->scope = $scope;

        foreach ($this->childComponents as $actionContainer) {
            $action = $actionContainer->action;
            if ($action instanceof ApprovalAction) {
                $action->scope($this->scope);
            }
        }

        return $this;
    }

    public function statusCategoryColors(array $colors): static
    {
        foreach ($this->childComponents as $actionContainer) {
            $action = $actionContainer->action;
            if ($action instanceof ApprovalAction) {
                $action->statusCategoryColors($colors);
            }
        }

        return $this;
    }

    public function approvalFlow(ApprovalFlow $approvalFlow): static
    {
        // pass category, scope to the approval flow
        $this->approvalFlow = $approvalFlow;

        foreach ($this->childComponents as $actionContainer) {
            $action = $actionContainer->action;
            if ($action instanceof ApprovalAction) {
                $action->approvalFlow($this->approvalFlow);
                $action->disabled(
                    fn ($record) => $this->approvalFlow->shouldDisable($record, $this->category, $this->scope, $action->getStatusEnumClass())
                );
                $action->visible(fn () => $this->approvalFlow->shouldBeVisible());

            }
        }

        return $this;
    }
}
