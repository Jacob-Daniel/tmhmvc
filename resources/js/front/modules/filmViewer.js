const viewer = document.querySelector('[data-viewer]');
export function initFilmViewer(pcid = 2, endpoint = '/inc/extracontent.php') {
    document.addEventListener('click', e => {
        let trigger = e.target.closest('[data-trigger]');
        if(!viewer) return;
        if (!trigger) return; // ignore clicks outside triggers

        const itemId = trigger.dataset.itemid?.replace('item_', '');
        const prodId = trigger.dataset.prodid;
        const catId = trigger.dataset.catid;

        if (!itemId || !prodId || !catId) {
            showViewerMessage('Unable to load item details.');
            return;
        }

        loadItem(itemId, prodId, catId, pcid, endpoint);
        if (trigger.matches('[data-close]')) {
            closeViewer();
        }    
    });
}

function handleTriggerClick(e, trigger,pcid,endpoint) {
    e.preventDefault();

    const { itemid, prodid, catid } = trigger.dataset;
    if (!itemid || !prodid || !catid) return;

    loadItem(itemid, prodid, catid,pcid,endpoint);
}

async function loadItem(itemId, prodId, catId, pcid, endpoint) {
    try {
        const postData = new URLSearchParams({
            id: itemId,
            cat_id: catId,
            prod_id: prodId,
            pcid: pcid
        });

        const response = await fetch(endpoint, {
            method: 'POST',
            body: postData
        });

        if (!response.ok) {
            const errText = await response.text();
            throw new Error(errText || `HTTP ${response.status}`);
        }

        const data = await response.json();

        if (data.error) throw new Error(data.error);

        const cleanDesc = data.desc?.replace(/<p>(&nbsp;|\s)*<\/p>/gi, '');
        setHTML('[data-h2-container]', data.h2);
        setHTML('[data-video-container]', data.video);
        setHTML('[data-desc-container]', cleanDesc);
        openViewer();

    } catch (err) {
        console.error('Failed to load content:', err);
        showViewerMessage(`Failed to load content: ${err.message}`);
    }
}


function setHTML(selector, html) {
    const el = viewer.querySelector(selector);
    if (el) el.innerHTML = html ?? '';
}

function openViewer() {
    viewer.classList.remove('hidden');
    void viewer.offsetWidth;
    viewer.classList.remove('opacity-0');
    const content = viewer.querySelector('[data-viewer-container]');
    content.classList.remove('scale-x-50');
    content.classList.add('scale-x-100');
    // document.body.classList.add('overflow-hidden');
}

function closeViewer() {
    const content = viewer.querySelector('[data-viewer-container]');
    content.classList.remove('scale-x-100');
    content.classList.add('scale-x-90');
    setTimeout(() => {
        viewer.classList.add('opacity-0');
    }, 50);    
    setTimeout(() => {
        viewer.classList.add('hidden');
        setHTML('[data-h2-container]', '');
        setHTML('[data-video-container]', '');
        setHTML('[data-desc-container]', '');
    }, 200);  
}

function showViewerMessage(message) {
    const container = document.querySelector('[data-video-container]');
    const desc = document.querySelector('[data-desc-container]');
    if (!container || !desc) return;
    setHTML('[data-h2-container]', '');
    setHTML('[data-video-container]', '');
    setHTML('[data-desc-container]', '<div class="text-red-400 p-4 bg-black/50 rounded">Error loading content</div>');

    openViewer();
} 

if(viewer) {
    viewer.addEventListener('click', (e) => {
        if (e.target === viewer) { 
            closeViewer();
        }
           if (e.target.matches('[data-close]')) {
            closeViewer();
        }  
    } );
}