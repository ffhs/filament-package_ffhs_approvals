<?php


namespace Ffhs\Approvals\Traits;


use Closure;

trait HasApprovalActionModifications
{

    private array|Closure $approvalActionsGroupLabel= [];
    private ?array $cachedApprovalActionsGroupLabel = null;

    private array|Closure $approvalActionsLabel= [];
    private ?array $cachedApprovalActionsLabel = null;

    private string|array|Closure $approvalActionsColor= [];
    private string|array|null $cachedApprovalActionsColor = null;

    private array|Closure $approvalActionsSelectColor= [];
    private string|array|null $cachedApprovalActionsSelectColor = null;


    private array|Closure $approvalActionsIcons= [];
    private ?array $cachedApprovalActionsIcons = null;


    private bool|Closure $isApprovalActionsDisabled= false;
    private ?bool $cachedIsApprovalActionsDisabled = null;


    private array|Closure $approvalActionToolTips= [];
    private ?array $cacheApprovalActionToolTips = null;




    private array|Closure $statusCategoryColors= [
        'approved' => 'success',
        'denied' => 'danger',
        'pending' => 'info',
    ];
    private string|array|null $cachedStatusCategoryColors = null;


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

    public function approvalActionsColor(string|array|Closure $approvalActionsColor): static
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

    public function approvalActionsSelectColor(string|array|Closure $approvalActionsSelectColor): static
    {
        $this->approvalActionsSelectColor = $approvalActionsSelectColor;
        $this->cachedApprovalActionsSelectColor= null;
        return $this;
    }
    public function getApprovalActionsSelectColor(): array
    {
        if(!is_null($this->cachedApprovalActionsSelectColor)) return $this->cachedApprovalActionsSelectColor;
        $this->cachedApprovalActionsSelectColor = $this->evaluate($this->approvalActionsSelectColor);
        return $this->cachedApprovalActionsSelectColor;
    }

    public function statusCategoryColors(string|array|Closure $statusCategoryColors): static
    {
        $this->statusCategoryColors = $statusCategoryColors;
        $this->cachedStatusCategoryColors= null;
        return $this;
    }
    public function getStatusCategoryColors(): array
    {
        if(!is_null($this->cachedStatusCategoryColors)) return $this->cachedStatusCategoryColors;
        $this->cachedStatusCategoryColors = $this->evaluate($this->statusCategoryColors);
        return $this->cachedStatusCategoryColors;
    }

    public function approvalActionsIcons(array|Closure $approvalActionsIcons): static
    {
        $this->approvalActionsIcons = $approvalActionsIcons;
        $this->cachedApprovalActionsIcons= null;
        return $this;
    }
    public function getApprovalActionsIcons(): array
    {
        if(!is_null($this->cachedApprovalActionsIcons)) return $this->cachedApprovalActionsIcons;
        $this->cachedApprovalActionsIcons = $this->evaluate($this->approvalActionsIcons);
        return $this->cachedApprovalActionsIcons;
    }


    public function approvalActionToolTips(array|Closure $approvalActionToolTips): static
    {
        $this->approvalActionToolTips = $approvalActionToolTips;
        $this->cacheApprovalActionToolTips= null;
        return $this;
    }
    public function getApprovalActionToolTips(): array
    {
        if(!is_null($this->cacheApprovalActionToolTips)) return $this->cacheApprovalActionToolTips;
        $this->cacheApprovalActionToolTips = $this->evaluate($this->approvalActionToolTips);
        return $this->cacheApprovalActionToolTips;
    }




    public function disableApprovalActions(bool|Closure $isApprovalActionsDisabled = true): static
    {
        $this->isApprovalActionsDisabled = $isApprovalActionsDisabled;
        $this->cachedIsApprovalActionsDisabled= null;
        return $this;
    }
    public function isApprovalActionsDisabled(): bool
    {
        if(!is_null($this->cachedIsApprovalActionsDisabled)) return $this->cachedIsApprovalActionsDisabled;
        $this->cachedIsApprovalActionsDisabled = $this->evaluate($this->isApprovalActionsDisabled);
        return $this->cachedIsApprovalActionsDisabled;
    }



}
