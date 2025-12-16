<div class="menu-item depth-{{ $depth ?? 0 }}" data-id="{{ $item->id }}" data-depth="{{ $depth ?? 0 }}">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 flex-1">
            <div class="drag-handle w-5 h-5 flex flex-col justify-center items-center text-gray-400 cursor-grab hover:text-gray-600" title="Kéo thả để sắp xếp">
                <div class="w-1 h-1 bg-current rounded-full mb-0.5"></div>
                <div class="w-1 h-1 bg-current rounded-full mb-0.5"></div>
                <div class="w-1 h-1 bg-current rounded-full mb-0.5"></div>
                <div class="w-1 h-1 bg-current rounded-full mb-0.5"></div>
                <div class="w-1 h-1 bg-current rounded-full"></div>
            </div>
            <div class="flex-1">
                <div class="font-medium text-sm">{{ $item->title }}</div>
                <div class="text-xs text-gray-500">
                    @if($item->linkable_type)
                        {{ class_basename($item->linkable_type) }}
                    @elseif($item->url)
                        Link → {{ $item->url }}
                    @endif
                </div>
            </div>
        </div>
        <div class="flex gap-1">
            <!-- Move Up -->
            <button onclick="moveUp({{ $item->id }})" class="p-1 hover:bg-blue-100 text-blue-600 rounded" title="Di chuyển lên">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
            </button>
            <!-- Move Down -->
            <button onclick="moveDown({{ $item->id }})" class="p-1 hover:bg-blue-100 text-blue-600 rounded" title="Di chuyển xuống">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <!-- Indent Right -->
            @if(($depth ?? 0) < 3)
            <button onclick="indentRight({{ $item->id }})" class="p-1 hover:bg-green-100 text-green-600 rounded" title="Tạo menu con">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
            @endif
            <!-- Indent Left -->
            @if(($depth ?? 0) > 0)
            <button onclick="indentLeft({{ $item->id }})" class="p-1 hover:bg-orange-100 text-orange-600 rounded" title="Hủy phân cấp">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            @endif
            <!-- Delete -->
            <button onclick="deleteItem({{ $item->id }})" class="p-1 hover:bg-red-100 text-red-600 rounded" title="Xóa">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>
        </div>
    </div>
    

</div>


