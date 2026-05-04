<div id="image-modal" class="fixed inset-0 z-50 hidden" aria-modal="true" role="dialog">
    <div id="image-modal-backdrop" class="absolute inset-0 bg-black/60"></div>
    <div class="relative z-10 mx-auto mt-10 w-full max-w-4xl bg-white rounded-xl shadow-xl overflow-hidden">
        <div class="flex items-center justify-between px-4 py-3 border-b bg-gray-50">
            <h2 class="font-semibold text-gray-700">Select Image</h2>
            <button id="image-modal-close"
                    class="text-gray-400 hover:text-gray-600 transition text-xl leading-none"
                    aria-label="Close">
                &times;
            </button>
        </div>
        <header id="header" class="flex flex-wrap items-center gap-3 px-4 py-3 border-b bg-gray-50">
            <input
                id="modal-search"
                placeholder="Search file name"
                class="border rounded px-2 py-1 w-56"
            />

            <div class="pagination ml-auto">
                <?php
                $pageinfo = setupPaging('images', 20);
                draw_modal_pager( $pageinfo['pages'],$pageinfo['page']);
                ?>
            </div>
        </header>
        <div id="restab" class="overflow-y-auto max-h-[60vh] p-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
        </div>
    </div>
</div>				