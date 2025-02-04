<?php


namespace Ffhs\Approvals\Traits;


use Closure;

trait HasApprovalActionModifications
{

    protected array|Closure $approvalActionsGroupLabel= [];
    protected ?array $cachedApprovalActionsGroupLabel = null;

    protected array|Closure $approvalActionsLabel= [];
    protected ?array $cachedApprovalActionsLabel = null;

    protected string|array|Closure $approvalActionsColor= [];
    protected string|array|null $cachedApprovalActionsColor = null;

    protected array|Closure $approvalActionsSelectColor= [];
    protected string|array|null $cachedApprovalActionsSelectColor = null;


    protected array|Closure $statusCategoryColors= [
        'approved' => 'success',
        'declined' => 'danger',
        'pending' => 'info',
    ];
    protected string|array|null $cachedStatusCategoryColors = null;


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
        if(!is_null($this->cachedStatusCategoryColors)) return $this->statusCategoryColors;
        $this->cachedStatusCategoryColors = $this->evaluate($this->statusCategoryColors);
        return $this->cachedStatusCategoryColors;
    }




}
