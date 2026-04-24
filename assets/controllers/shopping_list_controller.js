import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["newListName", "listsContainer", "itemsContainer", "newItemName", "newItemAmount"];
    static values = { id: String };

    connect() {
        this.db = null;
        this.initDB().then(() => {
            if (this.hasIdValue) {
                this.loadListDetails();
                this.loadItems();
            } else {
                this.loadLists();
            }
            this.sync();
        });

        window.addEventListener('online', () => document.body.classList.remove('is-offline'));
        window.addEventListener('offline', () => document.body.classList.add('is-offline'));
        if (!navigator.onLine) document.body.classList.add('is-offline');
    }

    async initDB() {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open("ncpListDB", 1);
            request.onupgradeneeded = (event) => {
                const db = event.target.result;
                if (!db.objectStoreNames.contains("lists")) {
                    db.createObjectStore("lists", { keyPath: "id" });
                }
                if (!db.objectStoreNames.contains("items")) {
                    db.createObjectStore("items", { keyPath: "id" });
                }
                if (!db.objectStoreNames.contains("sync_queue")) {
                    db.createObjectStore("sync_queue", { keyPath: "temp_id", autoIncrement: true });
                }
            };
            request.onsuccess = (event) => {
                this.db = event.target.result;
                resolve();
            };
            request.onerror = (event) => reject(event.target.error);
        });
    }

    // --- LIST LOGIC ---

    async loadLists() {
        if (!this.hasListsContainerTarget) return;
        const transaction = this.db.transaction(["lists"], "readonly");
        const store = transaction.objectStore("lists");
        const request = store.getAll();
        request.onsuccess = () => this.renderLists(request.result);
    }

    renderLists(lists) {
        if (lists.length === 0) {
            this.listsContainerTarget.innerHTML = '<p>Keine Listen vorhanden.</p>';
            return;
        }
        this.listsContainerTarget.innerHTML = lists.map(list => `
            <div class="list-item">
                <span class="name"><b>${list.name}</b></span>
                <a href="/lists/${list.id}" class="btn btn-primary" style="padding: 0.3rem 0.6rem; font-size: 0.8rem;">Öffnen</a>
                <button class="btn btn-danger" style="padding: 0.3rem 0.6rem; font-size: 0.8rem;" data-action="click->shopping-list#deleteList" data-id="${list.id}">Löschen</button>
            </div>
        `).join('');
    }

    async createList() {
        const name = this.newListNameTarget.value.trim();
        if (!name) return;
        const newList = { id: crypto.randomUUID(), name, description: "", state: "active", createdAt: new Date().toISOString(), updatedAt: new Date().toISOString() };
        const tx = this.db.transaction(["lists", "sync_queue"], "readwrite");
        tx.objectStore("lists").add(newList);
        tx.objectStore("sync_queue").add({ action: 'CREATE_LIST', data: newList });
        this.newListNameTarget.value = "";
        this.loadLists();
        this.sync();
    }

    async deleteList(event) {
        const id = event.currentTarget.dataset.id;
        if (!confirm('Liste wirklich löschen?')) return;
        const tx = this.db.transaction(["lists", "sync_queue"], "readwrite");
        tx.objectStore("lists").delete(id);
        tx.objectStore("sync_queue").add({ action: 'DELETE_LIST', id });
        this.loadLists();
        this.sync();
    }

    // --- ITEM LOGIC ---

    async loadListDetails() {
        const tx = this.db.transaction(["lists"], "readonly");
        const request = tx.objectStore("lists").get(this.idValue);
        request.onsuccess = () => {
            if (request.result) document.getElementById('list-name-display').textContent = request.result.name;
        };
    }

    async loadItems() {
        if (!this.hasItemsContainerTarget) return;
        const tx = this.db.transaction(["items"], "readonly");
        const request = tx.objectStore("items").getAll();
        request.onsuccess = () => {
            const items = request.result.filter(item => item.listId === this.idValue);
            this.renderItems(items);
        };
    }

    renderItems(items) {
        if (items.length === 0) {
            this.itemsContainerTarget.innerHTML = '<p>Keine Artikel in dieser Liste.</p>';
            return;
        }
        this.itemsContainerTarget.innerHTML = items.map(item => `
            <div class="list-item">
                <input type="checkbox" ${item.state === 'done' ? 'checked' : ''} 
                    data-action="change->shopping-list#toggleItem" data-id="${item.id}">
                <span class="name ${item.state === 'done' ? 'done' : ''}">${item.amount}x ${item.name}</span>
                <button class="btn btn-danger" style="padding: 0.2rem 0.5rem; font-size: 0.7rem;" 
                    data-action="click->shopping-list#deleteItem" data-id="${item.id}">X</button>
            </div>
        `).join('');
    }

    async addItem() {
        const name = this.newItemNameTarget.value.trim();
        const amount = parseFloat(this.newItemAmountTarget.value) || 1;
        if (!name) return;

        const newItem = {
            id: crypto.randomUUID(),
            listId: this.idValue,
            name,
            amount,
            state: 'open',
            updatedAt: new Date().toISOString()
        };

        const tx = this.db.transaction(["items", "sync_queue"], "readwrite");
        tx.objectStore("items").add(newItem);
        tx.objectStore("sync_queue").add({ action: 'CREATE_ITEM', listId: this.idValue, data: newItem });
        
        this.newItemNameTarget.value = "";
        this.newItemAmountTarget.value = "1";
        this.loadItems();
        this.sync();
    }

    async toggleItem(event) {
        const id = event.currentTarget.dataset.id;
        const state = event.currentTarget.checked ? 'done' : 'open';
        
        const tx = this.db.transaction(["items", "sync_queue"], "readwrite");
        const store = tx.objectStore("items");
        const request = store.get(id);
        
        request.onsuccess = () => {
            const item = request.result;
            item.state = state;
            item.updatedAt = new Date().toISOString();
            store.put(item);
            tx.objectStore("sync_queue").add({ action: 'UPDATE_ITEM', id, data: { state } });
            this.loadItems();
            this.sync();
        };
    }

    async deleteItem(event) {
        const id = event.currentTarget.dataset.id;
        const tx = this.db.transaction(["items", "sync_queue"], "readwrite");
        tx.objectStore("items").delete(id);
        tx.objectStore("sync_queue").add({ action: 'DELETE_ITEM', id });
        this.loadItems();
        this.sync();
    }

    // --- SYNC LOGIC ---

    async sync() {
        if (!navigator.onLine) return;
        const statusEl = document.getElementById('sync-status');
        if (statusEl) statusEl.textContent = "Sync...";

        try {
            await this.processSyncQueue();
            await this.fetchFromServer();
            if (statusEl) statusEl.textContent = "Bereit (" + new Date().toLocaleTimeString() + ")";
        } catch (e) {
            if (statusEl) statusEl.textContent = "Sync-Fehler";
        }
    }

    async processSyncQueue() {
        const token = localStorage.getItem('jwt_token');
        if (!token) return;

        const tx = this.db.transaction(["sync_queue"], "readonly");
        const queue = await new Promise(r => tx.objectStore("sync_queue").getAll().onsuccess = e => r(e.target.result));

        for (const item of queue) {
            try {
                let url = '', method = 'POST', body = null;
                switch(item.action) {
                    case 'CREATE_LIST': url = '/api/lists'; body = item.data; break;
                    case 'DELETE_LIST': url = `/api/lists/${item.id}`; method = 'DELETE'; break;
                    case 'CREATE_ITEM': url = `/api/lists/${item.listId}/items`; body = item.data; break;
                    case 'UPDATE_ITEM': url = `/api/lists/items/${item.id}`; method = 'PUT'; body = item.data; break;
                    case 'DELETE_ITEM': url = `/api/lists/items/${item.id}`; method = 'DELETE'; break;
                }

                const res = await fetch(url, {
                    method,
                    headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                    body: body ? JSON.stringify(body) : null
                });

                if (res.ok || res.status === 404) {
                    const delTx = this.db.transaction(["sync_queue"], "readwrite");
                    delTx.objectStore("sync_queue").delete(item.temp_id);
                }
            } catch (e) { console.error(e); }
        }
    }

    async fetchFromServer() {
        const token = localStorage.getItem('jwt_token');
        if (!token) return;

        const res = await fetch('/api/lists', { headers: { 'Authorization': 'Bearer ' + token } });
        if (res.ok) {
            const serverLists = await res.json();
            const tx = this.db.transaction(["lists", "items"], "readwrite");
            tx.objectStore("lists").clear();
            tx.objectStore("items").clear();
            
            for (const list of serverLists) {
                tx.objectStore("lists").add({ id: list.id, name: list.name, state: list.state });
                if (list.items) {
                    list.items.forEach(item => {
                        tx.objectStore("items").add({
                            id: item.id,
                            listId: list.id,
                            name: item.name,
                            amount: item.amount,
                            state: item.state
                        });
                    });
                }
            }
            this.hasIdValue ? this.loadItems() : this.loadLists();
        }
    }
}
