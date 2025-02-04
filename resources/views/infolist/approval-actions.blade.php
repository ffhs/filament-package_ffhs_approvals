
<x-dynamic-component :component="$getEntryWrapperView()">
    @foreach ($getApprovalFlow()->getApprovalBys() as $approvalBys)

        <p class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
            {{$getApprovalActionsGroupLabel()[$approvalBys->getName()] ?? $approvalBys->getName()}}
        </p>


       <div class="pt-2">
           @foreach ($getApprovalByActions($approvalBys) as $action)
               {{--        @if ($action->isVisible())--}}
               {{ $action }}
               {{--            @endif--}}
           @endforeach
       </div>

        <br/>
        <br/>
    @endforeach

</x-dynamic-component>
