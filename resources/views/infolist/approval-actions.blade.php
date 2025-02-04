
<x-dynamic-component :component="$getEntryWrapperView()">
    <div>
        @foreach ($getApprovalFlow()->getApprovalBys() as $approvalBys)

           <div>
               <p class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                   {{$getApprovalActionsGroupLabel()[$approvalBys->getName()] ?? $approvalBys->getName()}}
               </p>


               <div class="pt-2">
                   <x-filament::actions
                       :actions="$getChildComponentContainer($approvalBys->getName())->getComponents()"
                       :alignment="$getAlignment()"
                       :full-width="$isFullWidth()"
                   />
               </div>

               <br/>
               <br/>
           </div>
        @endforeach
    </div>

</x-dynamic-component>
