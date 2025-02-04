<?php


namespace Ffhs\Approvals\Traits;


use Closure;

trait HasApprovalActionModifications
{

    protected array|Closure $approvalActionsGroupLabel= [];
    protected ?array $cachedApprovalActionsGroupLabel = null;

    protected array|Closure $approvalActionsLabel= [];
    protected ?array $cachedApprovalActionsLabel = null;

    protected array|Closure $approvalActionsColor= [];
    protected ?array $cachedApprovalActionsColor = null;

    public function approvalActionsGroupLabel(array|Closure $approvalActionsGroupLabel): static
    {
        $this->approvalActionsGroupLabel = $approvalActionsGroupLabel;
        $this->cachedApprovalActionsGroupLabel = null;
        return $this;
    }
    public function getApprovalActionsGroupLabel(): array
    {
        if(!is_null($this->cachedApprovalActionsGroupLabel)) return $this->cachedApprovalActionsGroupLabel;
        $this->cachedApprovalActionsGroupLabel = $this->evaluate($this->approvalActionsGroupLabel);
        return $this->cachedApprovalActionsGroupLabel;
    }

    public function approvalActionsLabel(array|Closure $approvalActionsLabel): static
    {
        $this->approvalActionsLabel = $approvalActionsLabel;
        $this->cachedApprovalActionsLabel = null;
        return $this;
    }
    public function getApprovalActionsLabel(): array
    {
        if(!is_null($this->cachedApprovalActionsLabel)) return $this->cachedApprovalActionsLabel;
        $this->cachedApprovalActionsLabel = $this->evaluate($this->approvalActionsLabel);
        return $this->cachedApprovalActionsLabel;
    }

    public function approvalActionsColor(array|Closure $approvalActionsColor): static
    {
        $this->approvalActionsColor = $approvalActionsColor;
        $this->cachedApprovalActionsColor= null;
        return $this;
    }
    public function getApprovalActionsColor(): array
    {
        if(!is_null($this->cachedApprovalActionsColor)) return $this->cachedApprovalActionsColor;
        $this->cachedApprovalActionsColor = $this->evaluate($this->approvalActionsColor);
        return $this->cachedApprovalActionsColor;
    }


}
