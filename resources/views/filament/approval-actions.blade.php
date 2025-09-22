<x-dynamic-component :component="$getEntryWrapperView()">
    <div>
        @foreach ($getApprovalFlow()->getApprovalBys() as $approvalBys)
            <div>
                <p class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                    {{ $getGroupLabel($approvalBys->getName())}}
                </p>

                <div class="pt-2 mb-4">
                    <x-filament::actions
                        :actions="$getChildSchema($approvalBys->getName())->getComponents()"
                        :alignment="$getAlignment()"
                        :full-width="$isFullWidth()"
                    />
                </div>
            </div>
        @endforeach
    </div>
</x-dynamic-component>
