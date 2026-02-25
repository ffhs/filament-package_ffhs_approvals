---
sidebar_position: 1
---

# Advanced: Filtering Flows

You can filter approval checks by **flow categories** and/or **specific flow keys**.

## Assigning a category to a flow

To add a category to a flow, use `category()` when defining the flow:

```php
'my-flow-key' => \Ffhs\Approvals\Approval\SimpleApprovalFlow::make()
    ->category('my-category-key'),
```

## Filtering Approvals

The signature for `isApproved()`, `isDenied()`, `isOpen()` and `approved()` is:

```php
/**
 * @param string[]|null $categories
 * @param string[]|null $keys
 */
public function ...(?array $categories = null, ?array $keys = null): ...;
```

How filtering works:

- If no categories are provided (null), no filtering is applied.
- If you pass an array of categories, the result is calculated only from flows in those categories.
- If you pass an array of keys, the result is calculated only from the specified flow keys.

In all cases, you receive the aggregated result only for the matching flows (instead of all available approval flows).
