<div class="flex flex-col">
    <div class="flex items-end w-full">
        @if ($this->hasAnySearchableColumns())
            <div class="w-full">
                <div class="flex flex-col">
                    <label class="text-gray-500 text-sm sr-only" for="search">Search</label>
                    <input type="search" wire:model="search" id="search" name="search" placeholder="{{ __('Search') }}"
                           class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-gray-700"
                    />
                </div>
            </div>
        @endif
        <div class="@if ($this->hasAnySearchableColumns()) ml-4 @endif">
            <div class="flex flex-col">
                <label class="text-gray-500 text-sm whitespace-nowrap  sr-only"
                       for="resultsPerPage">{{ __('Results per page') }}</label>
                <select
                    id="resultsPerPage"
                    name="resultsPerPage"
                    class="form-select block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-gray-700"
                    wire:model="perPage">
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>
        <div class="flex">
            @foreach($actions as $k => $action)
                @if ($action->supportsBulk)
                    <button
                        @if (count($selected) === 0) disabled @endif
                    wire:click="bulkAction({{$k}})"
                        class="flex items-center whitespace-nowrap py-2 px-4 border border-gray-300 rounded-lg ml-4 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @if (count($selected) === 0) bg-gray-100 text-gray-500 @else bg-white text-gray-700 @endif">
                        <x-dynamic-component
                            :component="$action->icon"
                            class="w-5 h-5 text-gray-400"/>
                        <span class="inline-block ml-2">{{ $action->name }} </span>
                    </button>
                @endif
            @endforeach
        </div>
    </div>
    @if ($records->isNotEmpty())
        <div class="flex flex-col rounded-lg mt-4">
            <div class="-my-2 scrollbar-none overflow-x-auto">
                <div class="py-2 align-middle inline-block min-w-full ">
                    <div class="overflow-hidden border border-gray-300 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    wire:click="selectAll()"
                                    class="px-6 py-3 text-left cursor-pointer">
                                    <input
                                        @if (count($selected) === $records->total()) checked @endif
                                    type="checkbox"
                                        class="rounded text-blue-700 shadow-sm focus:border-blue-700 focus:ring focus:ring-blue-200 focus:ring-opacity-50 border-gray-300"
                                    />
                                </th>
                                @foreach($columns as $column)
                                    @if ($column->shouldRender())
                                        <th scope="col"
                                            @if ($column->sortable)
                                            wire:click.prevent="sortBy('{{ $column->name }}')"
                                            @endif
                                            class="px-6 py-3  @if ($column->sortable) cursor-pointer @endif">
                                            <button @if (!$column->sortable) disabled
                                                    @endif class="flex items-center text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap focus:outline-none">
                                                {{ $column->getLabel() }}

                                                @if($column->sortable && $sortColumn === $column->name)
                                                    <svg
                                                        class="ml-2 w-4 h-4 transition ease-in-out duration-150 @if ($sortDirection === 'asc') transform rotate-180 @endif"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                              d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                              clip-rule="evenodd"/>
                                                    </svg>
                                                @endif
                                            </button>
                                        </th>
                                    @endif
                                @endforeach
                            </tr>
                            </thead>
                            <tbody class="bg-white">
                            @forelse($records as $record)
                                <tr wire:click="select({{ $record->getKey() }})"
                                    class="cursor-pointer {{ $loop->index % 2 ? 'bg-gray-50' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input @if (in_array($record->getKey(), $selected)) checked @endif
                                        type="checkbox"
                                               class="rounded text-blue-700 shadow-sm focus:border-blue-700 focus:ring focus:ring-blue-200 focus:ring-opacity-50 border-gray-300"
                                        />
                                    </td>
                                    @foreach($columns as $column)
                                        @if ($column->kind === 'actions')
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center space-x-4 text-gray-500 text-sm">
                                                    @foreach($actions as $k => $action)
                                                        <button class="flex items-center"
                                                                wire:click="action({{ $record->getKey() }}, {{ $k }})">
                                                            @if ($action->icon)
                                                                <x-dynamic-component
                                                                    :component="$action->icon"
                                                                    class="w-5 h-5 text-gray-400"/>
                                                            @endif
                                                            <span class="inline-block ml-1">{{ $action->name }}</span>
                                                        </button>
                                                    @endforeach
                                                </div>
                                            </td>
                                        @elseif($column->shouldRender())
                                            <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-gray-500 text-sm">
                                                @if (is_null($column->getValueFor($record)))
                                                    ({{__('blank')}})
                                                @else
                                                    {{ $column->renderCell($record) }}
                                                @endif
                                        </span>
                                            </td>
                                        @endif
                                    @endforeach
                                </tr>
                            @empty
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4">
            {{ $records->links() }}
        </div>
    @else
        <div class="p-4 rounded-lg bg-white border mt-4 text-gray-700">
            <h1 class="">
                @empty($search)
                    {{ __('No results') }}
                @else
                    {{ __('No results found for') }} "{{ $search }}".
                @endempty
            </h1>
        </div>
    @endif
</div>
