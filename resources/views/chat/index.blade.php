@extends('layouts.dashboard')
@section('title', 'Chat')

@push('styles')
<style>
/* Override main-content padding so chat fills the viewport */
.main-content { padding: 60px 0 0 !important; height: 100vh; overflow: hidden; }

/* ─── Layout ─────────────────────────────────────────── */
.chat-wrap { display: flex; height: calc(100vh - 60px); background: #f7f8fc; }

/* Left panel */
.chat-left {
    width: 280px; flex-shrink: 0;
    border-right: 1px solid #eef0f6;
    background: #fff;
    display: flex; flex-direction: column;
    overflow: hidden;
}
.chat-left-header {
    padding: 14px 14px 10px;
    border-bottom: 1px solid #eef0f6;
    flex-shrink: 0;
}
.chat-search {
    border: 1px solid #e5e7eb; border-radius: 8px;
    padding: 6px 10px; font-size: 13px; width: 100%;
    outline: none; background: #f9fafb;
}
.chat-search:focus { border-color: #0066FF; background: #fff; }
.chat-rooms-list { flex: 1; overflow-y: auto; padding: 6px 0; }
.chat-room-item {
    padding: 10px 14px; cursor: pointer;
    border-left: 3px solid transparent;
    transition: background .12s;
    display: flex; align-items: center; gap: 10px;
}
.chat-room-item:hover { background: #f9fafb; }
.chat-room-item.active { background: #eff6ff; border-left-color: #0066FF; }
.chat-avatar {
    width: 36px; height: 36px; border-radius: 50%;
    background: #eff6ff; display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 700; color: #0066FF; flex-shrink: 0; overflow: hidden;
}
.chat-avatar img { width: 100%; height: 100%; object-fit: cover; }
.chat-room-name { font-size: 13.5px; font-weight: 600; color: #111827; line-height: 1.2; }
.chat-room-preview { font-size: 11.5px; color: #9ca3af; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 150px; }
.unread-badge { background: #0066FF; color: #fff; border-radius: 99px; font-size: 10px; font-weight: 700; padding: 2px 6px; min-width: 18px; text-align: center; }

/* Online section */
.chat-online-section { border-top: 1px solid #eef0f6; flex-shrink: 0; max-height: 180px; overflow-y: auto; }
.chat-online-title { font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #9ca3af; padding: 8px 14px 4px; }
.online-item { display: flex; align-items: center; gap: 8px; padding: 5px 14px; cursor: pointer; }
.online-item:hover { background: #f9fafb; }
.online-dot { width: 8px; height: 8px; background: #22c55e; border-radius: 50%; flex-shrink: 0; }

/* Right panel */
.chat-right { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
.chat-header {
    padding: 12px 18px;
    background: #fff; border-bottom: 1px solid #eef0f6;
    display: flex; align-items: center; gap: 12px; flex-shrink: 0;
}
.chat-header-name { font-size: 15px; font-weight: 700; color: #111827; }
.chat-header-sub  { font-size: 12px; color: #9ca3af; }
.chat-messages {
    flex: 1; overflow-y: auto;
    padding: 16px 20px; display: flex; flex-direction: column; gap: 4px;
}
.chat-input-area {
    padding: 10px 16px;
    background: #fff; border-top: 1px solid #eef0f6; flex-shrink: 0;
    display: flex; gap: 10px; align-items: flex-end;
}
#msgInput {
    flex: 1; resize: none; max-height: 100px;
    border: 1px solid #e5e7eb; border-radius: 10px;
    padding: 8px 12px; font-size: 13.5px; outline: none; line-height: 1.5;
}
#msgInput:focus { border-color: #0066FF; box-shadow: 0 0 0 3px rgba(0,102,255,.1); }

/* Messages */
.msg-group { margin-bottom: 6px; }
.msg-row { display: flex; align-items: flex-end; gap: 7px; margin-bottom: 2px; }
.msg-row.mine { flex-direction: row-reverse; }
.msg-bubble {
    max-width: 62%; padding: 8px 12px; border-radius: 14px;
    font-size: 13.5px; line-height: 1.55; word-break: break-word;
}
.msg-row.mine .msg-bubble { background: #0066FF; color: #fff; border-bottom-right-radius: 4px; }
.msg-row.theirs .msg-bubble { background: #fff; color: #374151; border-bottom-left-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,.07); }
.msg-meta { font-size: 10.5px; color: #9ca3af; padding: 0 4px; flex-shrink: 0; padding-bottom: 2px; }
.msg-sender-name { font-size: 11px; color: #9ca3af; margin-bottom: 3px; padding-left: 44px; }
.msg-row.mine .msg-sender-name { text-align: right; padding-right: 44px; padding-left: 0; }
.date-divider { text-align: center; font-size: 11px; color: #9ca3af; margin: 10px 0; }
.date-divider span { background: #f3f4f6; padding: 2px 10px; border-radius: 99px; }

/* Empty state */
.chat-empty { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; color: #9ca3af; }

/* Members popover */
.members-list { display: flex; flex-wrap: wrap; gap: 6px; }
.member-chip {
    display: flex; align-items: center; gap: 5px;
    background: #f3f4f6; border-radius: 99px;
    padding: 3px 10px 3px 6px; font-size: 12px;
}

@media(max-width: 767px) {
    .chat-left { width: 240px; }
    .chat-wrap { position: relative; }
}
</style>
@endpush

@section('content')
<div class="chat-wrap">

    {{-- ─── Left panel ─────────────────────────────────────────────────── --}}
    <div class="chat-left">
        <div class="chat-left-header">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span style="font-size:15px;font-weight:700;color:#111827;">Messages</span>
                @if(in_array(auth()->user()->role?->name, ['admin','hr','team_lead']))
                <button class="btn btn-sm btn-primary rounded-pill" style="font-size:11.5px;padding:3px 10px;"
                        data-bs-toggle="modal" data-bs-target="#newGroupModal">
                    <i class="bi bi-plus-lg me-1"></i>Group
                </button>
                @endif
            </div>
            <input class="chat-search" id="roomSearch" type="search" placeholder="Search rooms…">
        </div>

        <div class="chat-rooms-list" id="roomsList">
            <div class="text-center text-muted small py-4"><i class="bi bi-arrow-repeat spin"></i> Loading…</div>
        </div>

        <div class="chat-online-section">
            <div class="chat-online-title">Online Now <span id="onlineCount" class="text-success" style="font-weight:700;"></span></div>
            <div id="onlineList"></div>
        </div>
    </div>

    {{-- ─── Right panel ────────────────────────────────────────────────── --}}
    <div class="chat-right" id="chatRight">
        {{-- Placeholder --}}
        <div class="chat-empty" id="chatPlaceholder">
            <i class="bi bi-chat-dots" style="font-size:48px;opacity:.2;"></i>
            <p class="mt-3 mb-0" style="font-size:14px;">Select a conversation or start a DM</p>
            <small>Click any person in the Online section to start a chat</small>
        </div>

        {{-- Active chat (hidden until room opened) --}}
        <div id="activeChat" style="display:none;flex-direction:column;flex:1;overflow:hidden;">
            <div class="chat-header">
                <div class="chat-avatar" id="chatHeaderAvatar">A</div>
                <div class="flex-grow-1">
                    <div class="chat-header-name" id="chatHeaderName">—</div>
                    <div class="chat-header-sub" id="chatHeaderSub">—</div>
                </div>
                <button class="btn btn-sm btn-outline-secondary rounded-pill" style="font-size:11.5px;"
                        data-bs-toggle="modal" data-bs-target="#membersModal" id="membersBtn">
                    <i class="bi bi-people me-1"></i><span id="memberCount">—</span>
                </button>
            </div>

            <div class="chat-messages" id="chatMessages">
                <div class="chat-empty"><i class="bi bi-arrow-repeat spin" style="font-size:22px;"></i></div>
            </div>

            <div class="chat-input-area">
                <textarea id="msgInput" placeholder="Type a message… (Enter to send, Shift+Enter for new line)" rows="1"></textarea>
                <button class="btn btn-primary rounded-pill px-3" id="sendBtn" style="height:38px;">
                    <i class="bi bi-send-fill"></i>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ─── New Group Modal ──────────────────────────────────────────────────── --}}
<div class="modal fade" id="newGroupModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius:14px;">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">Create Group Chat</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Group Name <span class="text-danger">*</span></label>
                    <input type="text" id="groupName" class="form-control form-control-sm" placeholder="e.g. Design Team">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Add Members</label>
                    <div id="memberCheckboxes" style="max-height:200px;overflow-y:auto;border:1px solid #e5e7eb;border-radius:8px;padding:8px;">
                        @foreach($employees as $emp)
                        <div class="form-check py-1">
                            <input class="form-check-input" type="checkbox" name="group_members" value="{{ $emp->id }}" id="emp{{ $emp->id }}">
                            <label class="form-check-label small" for="emp{{ $emp->id }}">
                                {{ $emp->name }}
                                <span class="text-muted">— {{ $emp->designation ?? $emp->role?->name }}</span>
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button class="btn btn-light btn-sm rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary btn-sm rounded-pill px-4" id="createGroupBtn">Create</button>
            </div>
        </div>
    </div>
</div>

{{-- ─── Members Modal ────────────────────────────────────────────────────── --}}
<div class="modal fade" id="membersModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow" style="border-radius:14px;">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">Members</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="membersModalBody">—</div>
            <div class="modal-footer border-0 pt-0 flex-column align-items-stretch gap-2" id="addMemberSection" style="display:none!important;">
                <select class="form-select form-select-sm" id="addMemberSelect">
                    <option value="">— Add member —</option>
                    @foreach($employees as $emp)
                    <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                    @endforeach
                </select>
                <button class="btn btn-sm btn-outline-primary rounded-pill" id="addMemberBtn">Add Member</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const CSRF  = document.querySelector('meta[name="csrf-token"]').content;
const ME_ID = {{ auth()->id() }};

let currentRoom      = null;
let lastMessageId    = 0;
let pollTimer        = null;
let roomPollTimer    = null;
let pingTimer        = null;
let onlineTimer      = null;
let allRooms         = [];
let currentMembers   = [];
let currentIsAdmin   = false;

// ─── Boot ─────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    loadRooms();
    loadOnline();
    startPing();

    roomPollTimer = setInterval(loadRooms, 8000);
    onlineTimer   = setInterval(loadOnline, 15000);

    // Send on Enter (Shift+Enter = newline)
    document.getElementById('msgInput').addEventListener('keydown', e => {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
    });
    // Auto-grow textarea
    document.getElementById('msgInput').addEventListener('input', function () {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 100) + 'px';
    });

    document.getElementById('sendBtn').addEventListener('click', sendMessage);
    document.getElementById('createGroupBtn').addEventListener('click', createGroup);
    document.getElementById('addMemberBtn').addEventListener('click', addMember);
    document.getElementById('roomSearch').addEventListener('input', filterRooms);
});

// ─── Rooms ────────────────────────────────────────────────────────────────────
function loadRooms() {
    apiFetch('/dashboard/chat/rooms').then(rooms => {
        allRooms = rooms;
        renderRooms(rooms);
        updateSidebarBadge(rooms.reduce((s, r) => s + r.unread, 0));
    });
}

function renderRooms(rooms) {
    const q = document.getElementById('roomSearch').value.toLowerCase();
    const filtered = q ? rooms.filter(r => r.name.toLowerCase().includes(q)) : rooms;
    const list = document.getElementById('roomsList');
    if (!filtered.length) {
        list.innerHTML = '<div class="text-center text-muted small py-4">No conversations yet.</div>';
        return;
    }
    list.innerHTML = filtered.map(r => {
        const initials = r.name.charAt(0).toUpperCase();
        const avatarHtml = r.avatar
            ? `<div class="chat-avatar"><img src="${esc(r.avatar)}" alt=""></div>`
            : `<div class="chat-avatar">${initials}</div>`;
        const badge = r.unread ? `<span class="unread-badge">${r.unread}</span>` : '';
        const preview = r.last_message
            ? `${r.last_message.mine ? 'You: ' : ''}${esc(r.last_message.body)}`
            : '<em>No messages yet</em>';
        const time = r.last_message ? `<span style="font-size:10.5px;color:#9ca3af;">${esc(r.last_message.time)}</span>` : '';
        const typeIcon = r.type === 'group' ? '<i class="bi bi-people-fill" style="font-size:10px;color:#9ca3af;"></i> ' : '';
        return `<div class="chat-room-item${currentRoom?.id === r.id ? ' active' : ''}" onclick="openRoom(${r.id})">
            ${avatarHtml}
            <div style="flex:1;min-width:0;">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:4px;">
                    <span class="chat-room-name">${typeIcon}${esc(r.name)}</span>
                    <div style="display:flex;align-items:center;gap:4px;flex-shrink:0;">${time}${badge}</div>
                </div>
                <div class="chat-room-preview">${preview}</div>
            </div>
        </div>`;
    }).join('');
}

function filterRooms() { renderRooms(allRooms); }

// ─── Open Room ────────────────────────────────────────────────────────────────
function openRoom(roomId) {
    const room = allRooms.find(r => r.id === roomId);
    if (room) {
        currentRoom    = room;
        currentMembers = room.members || [];
        currentIsAdmin = room.is_admin;
        updateRoomHeader(room);
        document.getElementById('chatPlaceholder').style.display = 'none';
        document.getElementById('activeChat').style.display = 'flex';
        renderRooms(allRooms); // highlight active
    }

    lastMessageId = 0;
    document.getElementById('chatMessages').innerHTML =
        '<div class="chat-empty"><i class="bi bi-arrow-repeat spin" style="font-size:22px;"></i></div>';

    if (pollTimer) clearInterval(pollTimer);

    loadMessages(roomId).then(() => {
        markRead(roomId);
        pollTimer = setInterval(() => pollMessages(roomId), 3000);
    });
}

function updateRoomHeader(room) {
    document.getElementById('chatHeaderName').textContent = room.name;
    const onlineCount = (room.members || []).filter(m => m.online).length;
    document.getElementById('chatHeaderSub').textContent =
        room.type === 'direct'
            ? (onlineCount ? '● Online' : 'Offline')
            : `${room.members?.length || 0} members${onlineCount ? ` · ${onlineCount} online` : ''}`;

    const av = document.getElementById('chatHeaderAvatar');
    if (room.avatar) {
        av.innerHTML = `<img src="${esc(room.avatar)}" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">`;
    } else {
        av.textContent = room.name.charAt(0).toUpperCase();
    }

    document.getElementById('memberCount').textContent = room.members?.length || 0;

    // Show/hide add-member section based on admin status
    const addSection = document.getElementById('addMemberSection');
    if (room.type === 'group' && room.is_admin) {
        addSection.style.removeProperty('display');
    } else {
        addSection.style.setProperty('display', 'none', 'important');
    }
}

// ─── Messages ─────────────────────────────────────────────────────────────────
function loadMessages(roomId) {
    return apiFetch(`/dashboard/chat/rooms/${roomId}/messages`).then(data => {
        lastMessageId = data.last_id || 0;
        renderMessages(data.messages, false);
        scrollBottom();
    });
}

function pollMessages(roomId) {
    if (document.hidden) return; // don't poll when tab is hidden
    apiFetch(`/dashboard/chat/rooms/${roomId}/messages?after=${lastMessageId}`).then(data => {
        if (data.messages && data.messages.length > 0) {
            renderMessages(data.messages, true);
            lastMessageId = data.last_id;
            markRead(roomId);
            scrollBottom();
        }
    });
}

function renderMessages(messages, append) {
    const container = document.getElementById('chatMessages');
    if (!append) container.innerHTML = '';

    if (!messages.length && !append) {
        container.innerHTML = '<div class="date-divider"><span>No messages yet</span></div>';
        return;
    }

    let lastDate = null;
    messages.forEach(m => {
        if (m.date !== lastDate) {
            container.insertAdjacentHTML('beforeend',
                `<div class="date-divider"><span>${esc(m.date)}</span></div>`);
            lastDate = m.date;
        }
        const mine = m.mine;
        const avatarHtml = m.avatar
            ? `<img src="${esc(m.avatar)}" alt="" style="width:32px;height:32px;border-radius:50%;object-fit:cover;flex-shrink:0;">`
            : `<div class="chat-avatar" style="width:32px;height:32px;font-size:12px;flex-shrink:0;">${esc(m.initials)}</div>`;

        container.insertAdjacentHTML('beforeend', `
            <div class="msg-row ${mine ? 'mine' : 'theirs'}">
                ${!mine ? avatarHtml : ''}
                <div>
                    ${!mine ? `<div class="msg-sender-name" style="padding-left:0;">${esc(m.sender)}</div>` : ''}
                    <div style="display:flex;align-items:flex-end;gap:4px;${mine ? 'flex-direction:row-reverse;' : ''}">
                        <div class="msg-bubble">${esc(m.body).replace(/\n/g, '<br>')}</div>
                        <span class="msg-meta">${esc(m.time)}</span>
                    </div>
                </div>
                ${mine ? avatarHtml : ''}
            </div>`);
    });
}

function scrollBottom() {
    const el = document.getElementById('chatMessages');
    el.scrollTop = el.scrollHeight;
}

// ─── Send ─────────────────────────────────────────────────────────────────────
function sendMessage() {
    if (!currentRoom) return;
    const input = document.getElementById('msgInput');
    const body  = input.value.trim();
    if (!body) return;
    input.value = '';
    input.style.height = 'auto';

    apiPost(`/dashboard/chat/rooms/${currentRoom.id}/messages`, { body }).then(msg => {
        if (msg.id) {
            renderMessages([msg], true);
            lastMessageId = msg.id;
            scrollBottom();
        }
    });
}

// ─── Online ───────────────────────────────────────────────────────────────────
function loadOnline() {
    apiFetch('/dashboard/chat/online').then(users => {
        const list  = document.getElementById('onlineList');
        const count = document.getElementById('onlineCount');
        count.textContent = users.length ? `(${users.length})` : '';
        if (!users.length) {
            list.innerHTML = '<div class="text-muted small px-4 pb-2" style="font-size:11.5px;">No one else online</div>';
            return;
        }
        list.innerHTML = users.map(u => {
            const av = u.avatar
                ? `<img src="${esc(u.avatar)}" style="width:26px;height:26px;border-radius:50%;object-fit:cover;">`
                : `<div class="chat-avatar" style="width:26px;height:26px;font-size:11px;">${esc(u.name.charAt(0))}</div>`;
            return `<div class="online-item" onclick="startDM(${u.id})" title="Start DM with ${esc(u.name)}">
                ${av}
                <div class="online-dot"></div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:12.5px;font-weight:600;color:#111827;line-height:1.2;">${esc(u.name)}</div>
                    ${u.designation ? `<div style="font-size:10.5px;color:#9ca3af;">${esc(u.designation)}</div>` : ''}
                </div>
            </div>`;
        }).join('');
    });
}

function startDM(employeeId) {
    apiPost('/dashboard/chat/direct', { employee_id: employeeId }).then(data => {
        if (data.id) {
            loadRooms().then ? loadRooms() : null;
            apiFetch('/dashboard/chat/rooms').then(rooms => {
                allRooms = rooms;
                renderRooms(rooms);
                openRoom(data.id);
            });
        }
    });
}

// ─── Members Modal ────────────────────────────────────────────────────────────
document.getElementById('membersModal').addEventListener('show.bs.modal', () => {
    if (!currentRoom) return;
    const body = document.getElementById('membersModalBody');
    body.innerHTML = currentMembers.map(m => {
        const dot = m.online ? '<span class="online-dot me-1"></span>' : '';
        return `<div class="d-flex align-items-center justify-content-between py-1">
            <span style="font-size:13px;">${dot}${esc(m.name)}</span>
            ${currentIsAdmin && m.id !== ME_ID
                ? `<button class="btn btn-sm btn-link text-danger p-0" style="font-size:11px;" onclick="removeMember(${m.id})">Remove</button>`
                : ''}
        </div>`;
    }).join('') || '<p class="text-muted small">No members.</p>';
});

function removeMember(empId) {
    if (!currentRoom || !confirm('Remove this member?')) return;
    apiDelete(`/dashboard/chat/rooms/${currentRoom.id}/members/${empId}`).then(() => loadRooms());
}

function addMember() {
    const sel = document.getElementById('addMemberSelect');
    const id  = sel.value;
    if (!id || !currentRoom) return;
    apiPost(`/dashboard/chat/rooms/${currentRoom.id}/members`, { employee_id: id }).then(() => {
        sel.value = '';
        loadRooms();
    });
}

// ─── Create Group ─────────────────────────────────────────────────────────────
function createGroup() {
    const name = document.getElementById('groupName').value.trim();
    if (!name) { alert('Please enter a group name.'); return; }
    const memberIds = [...document.querySelectorAll('input[name="group_members"]:checked')].map(el => el.value);

    apiPost('/dashboard/chat/group', { name, member_ids: memberIds }).then(data => {
        if (data.id) {
            bootstrap.Modal.getInstance(document.getElementById('newGroupModal')).hide();
            document.getElementById('groupName').value = '';
            document.querySelectorAll('input[name="group_members"]').forEach(el => el.checked = false);
            apiFetch('/dashboard/chat/rooms').then(rooms => {
                allRooms = rooms;
                renderRooms(rooms);
                openRoom(data.id);
            });
        }
    });
}

// ─── Ping / Keep-alive ────────────────────────────────────────────────────────
function startPing() {
    apiPost('/dashboard/chat/ping');
    pingTimer = setInterval(() => apiPost('/dashboard/chat/ping'), 30000);
}

function markRead(roomId) {
    apiPost(`/dashboard/chat/rooms/${roomId}/mark-read`);
    // Clear unread in local data
    const r = allRooms.find(r => r.id === roomId);
    if (r) r.unread = 0;
    renderRooms(allRooms);
}

// ─── Sidebar unread badge ─────────────────────────────────────────────────────
function updateSidebarBadge(count) {
    let badge = document.getElementById('chatNavBadge');
    if (!badge) return;
    badge.textContent = count > 0 ? count : '';
    badge.style.display = count > 0 ? 'inline-flex' : 'none';
}

// ─── Helpers ──────────────────────────────────────────────────────────────────
function apiFetch(url) {
    return fetch(url, {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
    }).then(r => r.json()).catch(() => ({}));
}

function apiPost(url, body = {}) {
    return fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify(body),
    }).then(r => r.json()).catch(() => ({}));
}

function apiDelete(url) {
    return fetch(url, {
        method: 'DELETE',
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
    }).then(r => r.json()).catch(() => ({}));
}

function esc(str) {
    if (str == null) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>
<style>
@keyframes spin { to { transform: rotate(360deg); } }
.spin { display:inline-block; animation: spin 1s linear infinite; }
</style>
@endpush
