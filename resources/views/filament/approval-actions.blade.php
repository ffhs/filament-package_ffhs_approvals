<x-dynamic-component
    :component="$getFieldWrapperView()"
>
    <div>
        @foreach ($getApprovalFlow()->getApprovalBys() as $approvalBys)
            <div>
                <p style="font-size: 0.875rem; line-height: 1.5rem; font-weight: 500; color: #030712;">
                    {{ $getGroupLabel($approvalBys->getName())}}
                </p>

                <div style="padding-top: 0.5rem; margin-bottom: 1rem;">
                    {{$getChildSchema($approvalBys->getName())}}
                </div>
            </div>
        @endforeach
    </div>
</x-dynamic-component>
