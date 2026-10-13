@extends('admin.layout')

@section('title', 'Kelola Stand Kantin')
@section('page_title', 'Daftar Stand Kantin')
@section('page_subtitle', 'Kelola penjual kantin sekolah, nomor stand, jam buka, dan pemilik toko')

@section('content')

  <!-- 1. SUMMARY STATS -->
  <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(210px, 1fr)); gap:18px; margin-bottom:24px;">
    
    <div class="card-custom" style="padding:16px 20px; margin-bottom:0; display:flex; align-items:center; gap:14px;">
      <div style="width:48px; height:48px; border-radius:12px; background:var(--sky-soft); color:var(--sky); display:flex; align-items:center; justify-content:center; font-size:2.2rem;">
        <ion-icon name="storefront"></ion-icon>
      </div>
      <div>
        <div style="font-size:1.15rem; color:var(--text-muted); font-weight:600;">Total Stand</div>
        <div style="font-size:2rem; font-weight:800; color:var(--dark);">{{ $stands->count() }}</div>
      </div>
    </div>

    <div class="card-custom" style="padding:16px 20px; margin-bottom:0; display:flex; align-items:center; gap:14px;">
      <div style="width:48px; height:48px; border-radius:12px; background:var(--emerald-soft); color:var(--emerald); display:flex; align-items:center; justify-content:center; font-size:2.2rem;">
        <ion-icon name="radio-button-on"></ion-icon>
      </div>
      <div>
        <div style="font-size:1.15rem; color:var(--text-muted); font-weight:600;">Stand Buka</div>
        <div style="font-size:2rem; font-weight:800; color:#065f46;">{{ $stands->where('is_open', true)->count() }}</div>
      </div>
    </div>

    <div class="card-custom" style="padding:16px 20px; margin-bottom:0; display:flex; align-items:center; gap:14px;">
      <div style="width:48px; height:48px; border-radius:12px; background:var(--rose-soft); color:var(--rose); display:flex; align-items:center; justify-content:center; font-size:2.2rem;">
        <ion-icon name="close-circle"></ion-icon>
      </div>
      <div>
        <div style="font-size:1.15rem; color:var(--text-muted); font-weight:600;">Stand Tutup</div>
        <div style="font-size:2rem; font-weight:800; color:#991b1b;">{{ $stands->where('is_open', false)->count() }}</div>
      </div>
    </div>

    <div class="card-custom" style="padding:16px 20px; margin-bottom:0; display:flex; align-items:center; gap:14px;">
      <div style="width:48px; height:48px; border-radius:12px; background:var(--primary-soft); color:var(--primary); display:flex; align-items:center; justify-content:center; font-size:2.2rem;">
        <ion-icon name="restaurant"></ion-icon>
      </div>
      <div>
        <div style="font-size:1.15rem; color:var(--text-muted); font-weight:600;">Total Menu Terdaftar</div>
        <div style="font-size:2rem; font-weight:800; color:var(--dark);">{{ $stands->sum('menus_count') }}</div>
      </div>
    </div>

  </div>

  <!-- 2. ACTION BAR -->
  <div class="card-custom" style="padding:18px 22px; margin-bottom:24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
      <div>
        <h3 style="font-size:1.6rem; font-weight:800; color:var(--dark);">Semua Stand Kantin Sekolah</h3>
        <p style="font-size:1.25rem; color:var(--text-muted);">Kelola stan penjual, ganti status buka/tutup stan secara langsung</p>
      </div>

      <button type="button" class="btn-action btn-primary" onclick="openAddStandModal()">
        <ion-icon name="add-circle-outline"></ion-icon>
        <span>Tambah Stand Baru</span>
      </button>
    </div>
  </div>

  <!-- 3. STANDS TABLE -->
  <div class="card-custom">
    <div class="table-container">
      <table class="table-custom">
        <thead>
          <tr>
            <th style="width:90px;">No. Stand</th>
            <th>Nama Stand</th>
            <th>Pemilik / Akun</th>
            <th>Kontak HP</th>
            <th>Jam Operasional</th>
            <th>Menu Terdaftar</th>
            <th>Status Stand</th>
            <th style="text-align:right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($stands as $stand)
            <tr>
              <!-- No Stand -->
              <td>
                <span class="badge-status info" style="font-size:11.5px; font-weight:700;">
                  #{{ $stand->stand_number }}
                </span>
              </td>

              <!-- Nama Stand -->
              <td>
                <div style="font-weight:600; font-size:13.5px; color:var(--text);">{{ $stand->name }}</div>
                @if($stand->description)
                  <div style="font-size:11.5px; color:var(--text-muted); max-width:260px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                    {{ $stand->description }}
                  </div>
                @endif
              </td>

              <!-- Pemilik -->
              <td>
                @if($stand->user)
                  <div style="font-weight:600; font-size:12.5px; color:var(--text);">{{ $stand->user->name }}</div>
                  <div style="font-size:11px; color:var(--text-muted);">{{ $stand->user->email }}</div>
                @else
                  <span style="color:#94a3b8; font-style:italic; font-size:11.5px;">Belum ditautkan</span>
                @endif
              </td>

              <!-- Kontak -->
              <td>
                <div style="font-size:12px; font-weight:600;">
                  @if($stand->phone)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $stand->phone) }}" target="_blank" style="color:var(--emerald); text-decoration:none; display:inline-flex; align-items:center; gap:3px;">
                      <ion-icon name="logo-whatsapp"></ion-icon>
                      <span>{{ $stand->phone }}</span>
                    </a>
                  @else
                    <span style="color:#94a3b8;">-</span>
                  @endif
                </div>
              </td>

              <!-- Jam Operasional -->
              <td>
                <div style="font-size:12px; color:var(--text); font-weight:500;">
                  @if($stand->opening_time || $stand->closing_time)
                    {{ $stand->opening_time ?? '07:00' }} - {{ $stand->closing_time ?? '15:00' }} WIB
                  @else
                    <span style="color:#94a3b8;">07:00 - 15:00 WIB</span>
                  @endif
                </div>
              </td>

              <!-- Menu Terdaftar -->
              <td>
                <a href="{{ route('admin.menus.index', ['stand_id' => $stand->id]) }}" class="badge-status gray" style="text-decoration:none; font-size:11.5px; cursor:pointer;" title="Lihat menu stand ini">
                  <ion-icon name="restaurant-outline"></ion-icon>
                  <span>{{ $stand->menus_count }} Menu</span>
                </a>
              </td>

              <!-- Status Buka / Tutup Toggle -->
              <td>
                <form action="{{ route('admin.stands.toggle-open', $stand->id) }}" method="POST">
                  @csrf
                  @if($stand->is_open)
                    <button type="submit" class="badge-status success" style="border:none; cursor:pointer;" title="Klik untuk ubah jadi TUTUP">
                      <ion-icon name="radio-button-on"></ion-icon>
                      <span>BUKA</span>
                    </button>
                  @else
                    <button type="submit" class="badge-status danger" style="border:none; cursor:pointer;" title="Klik untuk ubah jadi BUKA">
                      <ion-icon name="close-circle"></ion-icon>
                      <span>TUTUP</span>
                    </button>
                  @endif
                </form>
              </td>

              <!-- Aksi -->
              <td style="text-align:right;">
                <div style="display:inline-flex; gap:6px;">
                  <button type="button" class="btn-action btn-secondary btn-sm" data-stand="{{ json_encode($stand) }}" onclick="openEditStandModal(this)" title="Edit Stand">
                    <ion-icon name="create-outline"></ion-icon>
                  </button>

                  <form action="{{ route('admin.stands.destroy', $stand->id) }}" method="POST" data-confirm="Hapus stand {{ $stand->name }} beserta seluruh menu di dalamnya?" onsubmit="return confirm(this.dataset.confirm);" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-action btn-secondary btn-sm" style="color:var(--rose);" title="Hapus Stand">
                      <ion-icon name="trash-outline"></ion-icon>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" style="text-align:center; padding:35px; color:#64748b;">
                Belum ada stand kantin yang terdaftar.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- 4. MODAL TAMBAH STAND -->
  <div class="modal-backdrop-custom" id="addStandModal" onclick="if(event.target===this) closeAddStandModal()">
    <div class="modal-card-custom">
      <div class="modal-header-custom">
        <h3>Tambah Stand Kantin Baru</h3>
        <button class="modal-close-btn" onclick="closeAddStandModal()">&times;</button>
      </div>

      <form action="{{ route('admin.stands.store') }}" method="POST">
        @csrf
        <div class="modal-body-custom">
          
          <div style="display:grid; grid-template-columns:120px 1fr; gap:12px;">
            <div class="form-group-admin">
              <label class="form-label-admin">No. Stand *</label>
              <input type="text" name="stand_number" class="form-control-admin" placeholder="01" required>
            </div>

            <div class="form-group-admin">
              <label class="form-label-admin">Nama Stand *</label>
              <input type="text" name="name" class="form-control-admin" placeholder="Stand Makanan Bu Ani" required>
            </div>
          </div>

          <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
            <div class="form-group-admin">
              <label class="form-label-admin">Akun Penjual / Pengelola</label>
              <select name="user_id" class="form-control-admin">
                <option value="">-- Pilih Akun (Opsional) --</option>
                @foreach($users as $usr)
                  <option value="{{ $usr->id }}">{{ $usr->name }} ({{ $usr->role }})</option>
                @endforeach
              </select>
            </div>

            <div class="form-group-admin">
              <label class="form-label-admin">Nomor HP / WhatsApp</label>
              <input type="text" name="phone" class="form-control-admin" placeholder="08123456789">
            </div>
          </div>

          <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
            <div class="form-group-admin">
              <label class="form-label-admin">Jam Buka</label>
              <input type="text" name="opening_time" class="form-control-admin" placeholder="07:00">
            </div>

            <div class="form-group-admin">
              <label class="form-label-admin">Jam Tutup</label>
              <input type="text" name="closing_time" class="form-control-admin" placeholder="15:00">
            </div>
          </div>

          <div class="form-group-admin">
            <label class="form-label-admin">Foto / Gambar Stand</label>
            <input type="text" name="image" class="form-control-admin" placeholder="assets/images/stand-1.jpg">
          </div>

          <div class="form-group-admin">
            <label class="form-label-admin">Deskripsi Stand (Opsional)</label>
            <textarea name="description" class="form-control-admin" rows="2" placeholder="Menyediakan aneka mie, bakso, dan jus segar"></textarea>
          </div>

          <div style="margin-top:10px;">
            <label style="display:flex; align-items:center; gap:8px; font-size:1.35rem; cursor:pointer;">
              <input type="checkbox" name="is_open" value="1" checked>
              <span>Stand Langsung Dibuka (Online)</span>
            </label>
          </div>

        </div>

        <div class="modal-footer-custom">
          <button type="button" class="btn-action btn-secondary" onclick="closeAddStandModal()">Batal</button>
          <button type="submit" class="btn-action btn-primary">Simpan Stand Baru</button>
        </div>
      </form>
    </div>
  </div>

  <!-- 5. MODAL EDIT STAND -->
  <div class="modal-backdrop-custom" id="editStandModal" onclick="if(event.target===this) closeEditStandModal()">
    <div class="modal-card-custom">
      <div class="modal-header-custom">
        <h3>Edit Data Stand Kantin</h3>
        <button class="modal-close-btn" onclick="closeEditStandModal()">&times;</button>
      </div>

      <form id="editStandForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body-custom">
          
          <div style="display:grid; grid-template-columns:120px 1fr; gap:12px;">
            <div class="form-group-admin">
              <label class="form-label-admin">No. Stand *</label>
              <input type="text" name="stand_number" id="edit_stand_number" class="form-control-admin" required>
            </div>

            <div class="form-group-admin">
              <label class="form-label-admin">Nama Stand *</label>
              <input type="text" name="name" id="edit_name" class="form-control-admin" required>
            </div>
          </div>

          <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
            <div class="form-group-admin">
              <label class="form-label-admin">Akun Penjual / Pengelola</label>
              <select name="user_id" id="edit_user_id" class="form-control-admin">
                <option value="">-- Tidak Ditautkan --</option>
                @foreach($users as $usr)
                  <option value="{{ $usr->id }}">{{ $usr->name }} ({{ $usr->role }})</option>
                @endforeach
              </select>
            </div>

            <div class="form-group-admin">
              <label class="form-label-admin">Nomor HP / WhatsApp</label>
              <input type="text" name="phone" id="edit_phone" class="form-control-admin">
            </div>
          </div>

          <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
            <div class="form-group-admin">
              <label class="form-label-admin">Jam Buka</label>
              <input type="text" name="opening_time" id="edit_opening_time" class="form-control-admin">
            </div>

            <div class="form-group-admin">
              <label class="form-label-admin">Jam Tutup</label>
              <input type="text" name="closing_time" id="edit_closing_time" class="form-control-admin">
            </div>
          </div>

          <div class="form-group-admin">
            <label class="form-label-admin">Foto / Gambar Stand</label>
            <input type="text" name="image" id="edit_image" class="form-control-admin">
          </div>

          <div class="form-group-admin">
            <label class="form-label-admin">Deskripsi Stand</label>
            <textarea name="description" id="edit_description" class="form-control-admin" rows="2"></textarea>
          </div>

          <div style="margin-top:10px;">
            <label style="display:flex; align-items:center; gap:8px; font-size:1.35rem; cursor:pointer;">
              <input type="checkbox" name="is_open" id="edit_is_open" value="1">
              <span>Stand Sedang Buka (Online)</span>
            </label>
          </div>

        </div>

        <div class="modal-footer-custom">
          <button type="button" class="btn-action btn-secondary" onclick="closeEditStandModal()">Batal</button>
          <button type="submit" class="btn-action btn-primary">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>

@endsection

@push('scripts')
<script>
  function openAddStandModal() {
    document.getElementById('addStandModal').classList.add('active');
  }
  function closeAddStandModal() {
    document.getElementById('addStandModal').classList.remove('active');
  }

  function openEditStandModal(source) {
    const stand = (source && source.dataset && source.dataset.stand) ? JSON.parse(source.dataset.stand) : source;
    document.getElementById('editStandForm').action = `/admin/stands/${stand.id}`;
    document.getElementById('edit_stand_number').value = stand.stand_number;
    document.getElementById('edit_name').value = stand.name;
    document.getElementById('edit_user_id').value = stand.user_id || '';
    document.getElementById('edit_phone').value = stand.phone || '';
    document.getElementById('edit_opening_time').value = stand.opening_time || '';
    document.getElementById('edit_closing_time').value = stand.closing_time || '';
    document.getElementById('edit_image').value = stand.image || '';
    document.getElementById('edit_description').value = stand.description || '';
    document.getElementById('edit_is_open').checked = Boolean(stand.is_open);

    document.getElementById('editStandModal').classList.add('active');
  }
  function closeEditStandModal() {
    document.getElementById('editStandModal').classList.remove('active');
  }
</script>
@endpush
