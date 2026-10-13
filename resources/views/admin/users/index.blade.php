@extends('admin.layout')

@section('title', 'Kelola Pengguna & Akun')
@section('page_title', 'Daftar Pengguna & Siswa')
@section('page_subtitle', 'Manajemen akun login administrator, pengelola stand kantin, guru, dan siswa')

@section('content')

  <!-- 1. ROLE TABS -->
  <div style="display:flex; gap:8px; margin-bottom:18px; overflow-x:auto; padding-bottom:4px;">
    <a href="{{ route('admin.users.index', ['role' => 'all', 'search' => $search]) }}" 
       class="btn-action {{ $role === 'all' ? 'btn-primary' : 'btn-secondary' }}" style="padding:6px 12px; font-size:12.5px;">
      <span>Semua</span>
      <span class="nav-badge" style="background:rgba(0,0,0,0.15);">{{ $roleCounts['all'] }}</span>
    </a>

    <a href="{{ route('admin.users.index', ['role' => 'admin', 'search' => $search]) }}" 
       class="btn-action {{ $role === 'admin' ? 'btn-primary' : 'btn-secondary' }}" style="padding:6px 12px; font-size:12.5px;">
      <ion-icon name="shield-checkmark"></ion-icon>
      <span>Admin</span>
      <span class="nav-badge" style="background:rgba(0,0,0,0.15);">{{ $roleCounts['admin'] }}</span>
    </a>

    <a href="{{ route('admin.users.index', ['role' => 'stand_owner', 'search' => $search]) }}" 
       class="btn-action {{ $role === 'stand_owner' ? 'btn-primary' : 'btn-secondary' }}" style="padding:6px 12px; font-size:12.5px;">
      <ion-icon name="storefront"></ion-icon>
      <span>Penjual Stand</span>
      <span class="nav-badge" style="background:rgba(0,0,0,0.15);">{{ $roleCounts['stand_owner'] }}</span>
    </a>

    <a href="{{ route('admin.users.index', ['role' => 'teacher', 'search' => $search]) }}" 
       class="btn-action {{ $role === 'teacher' ? 'btn-primary' : 'btn-secondary' }}" style="padding:6px 12px; font-size:12.5px;">
      <ion-icon name="school"></ion-icon>
      <span>Guru & Staf</span>
      <span class="nav-badge" style="background:rgba(0,0,0,0.15);">{{ $roleCounts['teacher'] }}</span>
    </a>

    <a href="{{ route('admin.users.index', ['role' => 'student', 'search' => $search]) }}" 
       class="btn-action {{ $role === 'student' ? 'btn-primary' : 'btn-secondary' }}" style="padding:6px 12px; font-size:12.5px;">
      <ion-icon name="person"></ion-icon>
      <span>Siswa</span>
      <span class="nav-badge" style="background:rgba(0,0,0,0.15);">{{ $roleCounts['student'] }}</span>
    </a>
  </div>

  <!-- 2. ACTION & SEARCH BAR -->
  <div class="card-custom" style="padding:18px 22px; margin-bottom:24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
      
      <!-- Search Form -->
      <form action="{{ route('admin.users.index') }}" method="GET" style="display:flex; gap:10px; flex:1; max-width:480px;">
        <input type="hidden" name="role" value="{{ $role }}">
        <div style="position:relative; width:100%;">
          <ion-icon name="search-outline" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); font-size:1.8rem; color:#94a3b8;"></ion-icon>
          <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, email, kelas, nomor HP..." class="form-control-admin" style="padding-left:38px;">
        </div>
        <button type="submit" class="btn-action btn-secondary" style="padding:10px 16px;">
          <span>Cari</span>
        </button>
        @if(!empty($search))
          <a href="{{ route('admin.users.index', ['role' => $role]) }}" class="btn-action btn-secondary" style="padding:10px 14px;">
            <span>Reset</span>
          </a>
        @endif
      </form>

      <!-- Add User Button -->
      <button type="button" class="btn-action btn-primary" onclick="openAddUserModal()">
        <ion-icon name="person-add-outline"></ion-icon>
        <span>Tambah Pengguna Baru</span>
      </button>

    </div>
  </div>

  <!-- 3. USERS TABLE -->
  <div class="card-custom">
    <div class="table-container">
      <table class="table-custom">
        <thead>
          <tr>
            <th>Nama Lengkap & Kontak</th>
            <th>Peran (Role)</th>
            <th>Kelas / Divisi</th>
            <th>NISN / NIP</th>
            <th>No. Telepon</th>
            <th>Terdaftar Sejak</th>
            <th style="text-align:right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $user)
            <tr>
              <!-- Nama & Email -->
              <td>
                <div style="display:flex; align-items:center; gap:9px;">
                  <div style="width:30px; height:30px; border-radius:50%; background:#f1f5f9; color:#475569; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:12px; border:1px solid #e2e8f0;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                  </div>
                  <div>
                    <div style="font-weight:600; font-size:13px; color:var(--text);">
                      {{ $user->name }}
                      @if($user->id === \Illuminate\Support\Facades\Auth::id())
                        <span style="font-size:10px; background:var(--primary-soft); color:var(--primary); padding:1px 5px; border-radius:3px; margin-left:3px; font-weight:700;">Anda</span>
                      @endif
                    </div>
                    <div style="font-size:11.5px; color:var(--text-muted);">{{ $user->email }}</div>
                  </div>
                </div>
              </td>

              <!-- Role Badge -->
              <td>
                @if($user->role === 'admin')
                  <span class="badge-status danger">
                    <ion-icon name="shield-checkmark"></ion-icon>
                    <span>Admin</span>
                  </span>
                @elseif($user->role === 'stand_owner')
                  <span class="badge-status warning">
                    <ion-icon name="storefront"></ion-icon>
                    <span>Penjual Stand</span>
                  </span>
                @elseif($user->role === 'teacher')
                  <span class="badge-status info">
                    <ion-icon name="school"></ion-icon>
                    <span>Guru</span>
                  </span>
                @elseif($user->role === 'staff')
                  <span class="badge-status info">
                    <ion-icon name="briefcase"></ion-icon>
                    <span>Staf</span>
                  </span>
                @else
                  <span class="badge-status success">
                    <ion-icon name="person"></ion-icon>
                    <span>Siswa</span>
                  </span>
                @endif
              </td>

              <!-- Kelas / Divisi -->
              <td>
                <div style="font-weight:500; font-size:12.5px; color:var(--text);">
                  {{ $user->class_or_division ?? '-' }}
                </div>
              </td>

              <!-- NISN / NIP -->
              <td>
                <span style="font-family:'JetBrains Mono', monospace; font-size:11.5px; color:#64748b;">
                  {{ $user->nisn_or_nip ?? '-' }}
                </span>
              </td>

              <!-- Phone -->
              <td>
                <div style="font-size:12px;">
                  @if($user->phone)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $user->phone) }}" target="_blank" style="color:var(--emerald); text-decoration:none; display:inline-flex; align-items:center; gap:3px; font-weight:600;">
                      <ion-icon name="logo-whatsapp"></ion-icon>
                      <span>{{ $user->phone }}</span>
                    </a>
                  @else
                    <span style="color:#94a3b8;">-</span>
                  @endif
                </div>
              </td>

              <!-- Registered At -->
              <td>
                <div style="font-size:11.5px; color:var(--text-muted);">
                  {{ $user->created_at->format('d M Y') }}
                </div>
              </td>

              <!-- Aksi -->
              <td style="text-align:right;">
                <div style="display:inline-flex; gap:6px;">
                  <button type="button" class="btn-action btn-secondary btn-sm" data-user="{{ json_encode($user) }}" onclick="openEditUserModal(this)" title="Edit Pengguna">
                    <ion-icon name="create-outline"></ion-icon>
                  </button>

                  @if($user->id !== \Illuminate\Support\Facades\Auth::id())
                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" data-confirm="Hapus akun {{ $user->name }}?" onsubmit="return confirm(this.dataset.confirm);" style="display:inline;">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn-action btn-secondary btn-sm" style="color:var(--rose);" title="Hapus Pengguna">
                        <ion-icon name="trash-outline"></ion-icon>
                      </button>
                    </form>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" style="text-align:center; padding:35px; color:#64748b;">
                Tidak ada pengguna yang sesuai dengan kriteria pencarian.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div style="margin-top:20px;">
      {{ $users->links() }}
    </div>
  </div>

  <!-- 4. MODAL TAMBAH PENGGUNA -->
  <div class="modal-backdrop-custom" id="addUserModal" onclick="if(event.target===this) closeAddUserModal()">
    <div class="modal-card-custom">
      <div class="modal-header-custom">
        <h3>Tambah Akun Pengguna Baru</h3>
        <button class="modal-close-btn" onclick="closeAddUserModal()">&times;</button>
      </div>

      <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        <div class="modal-body-custom">
          
          <div class="form-group-admin">
            <label class="form-label-admin">Nama Lengkap *</label>
            <input type="text" name="name" class="form-control-admin" placeholder="Contoh: Budi Santoso" required>
          </div>

          <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
            <div class="form-group-admin">
              <label class="form-label-admin">Email Login *</label>
              <input type="email" name="email" class="form-control-admin" placeholder="budi@smkn1ciomas.sch.id" required>
            </div>

            <div class="form-group-admin">
              <label class="form-label-admin">Password Awal *</label>
              <input type="password" name="password" class="form-control-admin" placeholder="Min. 6 karakter" required minlength="6">
            </div>
          </div>

          <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
            <div class="form-group-admin">
              <label class="form-label-admin">Peran (Role) *</label>
              <select name="role" class="form-control-admin" required>
                <option value="student">Siswa</option>
                <option value="teacher">Guru</option>
                <option value="staff">Staf Tata Usaha</option>
                <option value="stand_owner">Penjual / Pemilik Stand</option>
                <option value="admin">Administrator</option>
              </select>
            </div>

            <div class="form-group-admin">
              <label class="form-label-admin">Nomor HP / WhatsApp</label>
              <input type="text" name="phone" class="form-control-admin" placeholder="08123456789">
            </div>
          </div>

          <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
            <div class="form-group-admin">
              <label class="form-label-admin">Kelas / Jurusan / Divisi</label>
              <input type="text" name="class_or_division" class="form-control-admin" placeholder="Contoh: XII RPL 1">
            </div>

            <div class="form-group-admin">
              <label class="form-label-admin">NISN / NIP</label>
              <input type="text" name="nisn_or_nip" class="form-control-admin" placeholder="Contoh: 0054321987">
            </div>
          </div>

        </div>

        <div class="modal-footer-custom">
          <button type="button" class="btn-action btn-secondary" onclick="closeAddUserModal()">Batal</button>
          <button type="submit" class="btn-action btn-primary">Daftarkan Pengguna</button>
        </div>
      </form>
    </div>
  </div>

  <!-- 5. MODAL EDIT PENGGUNA -->
  <div class="modal-backdrop-custom" id="editUserModal" onclick="if(event.target===this) closeEditUserModal()">
    <div class="modal-card-custom">
      <div class="modal-header-custom">
        <h3>Edit Data Pengguna</h3>
        <button class="modal-close-btn" onclick="closeEditUserModal()">&times;</button>
      </div>

      <form id="editUserForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body-custom">
          
          <div class="form-group-admin">
            <label class="form-label-admin">Nama Lengkap *</label>
            <input type="text" name="name" id="edit_name" class="form-control-admin" required>
          </div>

          <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
            <div class="form-group-admin">
              <label class="form-label-admin">Email Login *</label>
              <input type="email" name="email" id="edit_email" class="form-control-admin" required>
            </div>

            <div class="form-group-admin">
              <label class="form-label-admin">Ganti Password (Kosongkan bila tetap)</label>
              <input type="password" name="password" class="form-control-admin" placeholder="Minimal 6 karakter" minlength="6">
            </div>
          </div>

          <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
            <div class="form-group-admin">
              <label class="form-label-admin">Peran (Role) *</label>
              <select name="role" id="edit_role" class="form-control-admin" required>
                <option value="student">Siswa</option>
                <option value="teacher">Guru</option>
                <option value="staff">Staf Tata Usaha</option>
                <option value="stand_owner">Penjual / Pemilik Stand</option>
                <option value="admin">Administrator</option>
              </select>
            </div>

            <div class="form-group-admin">
              <label class="form-label-admin">Nomor HP / WhatsApp</label>
              <input type="text" name="phone" id="edit_phone" class="form-control-admin">
            </div>
          </div>

          <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
            <div class="form-group-admin">
              <label class="form-label-admin">Kelas / Jurusan / Divisi</label>
              <input type="text" name="class_or_division" id="edit_class_or_division" class="form-control-admin">
            </div>

            <div class="form-group-admin">
              <label class="form-label-admin">NISN / NIP</label>
              <input type="text" name="nisn_or_nip" id="edit_nisn_or_nip" class="form-control-admin">
            </div>
          </div>

        </div>

        <div class="modal-footer-custom">
          <button type="button" class="btn-action btn-secondary" onclick="closeEditUserModal()">Batal</button>
          <button type="submit" class="btn-action btn-primary">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>

@endsection

@push('scripts')
<script>
  function openAddUserModal() {
    document.getElementById('addUserModal').classList.add('active');
  }
  function closeAddUserModal() {
    document.getElementById('addUserModal').classList.remove('active');
  }

  function openEditUserModal(source) {
    const user = (source && source.dataset && source.dataset.user) ? JSON.parse(source.dataset.user) : source;
    document.getElementById('editUserForm').action = `/admin/users/${user.id}`;
    document.getElementById('edit_name').value = user.name;
    document.getElementById('edit_email').value = user.email;
    document.getElementById('edit_role').value = user.role;
    document.getElementById('edit_phone').value = user.phone || '';
    document.getElementById('edit_class_or_division').value = user.class_or_division || '';
    document.getElementById('edit_nisn_or_nip').value = user.nisn_or_nip || '';

    document.getElementById('editUserModal').classList.add('active');
  }
  function closeEditUserModal() {
    document.getElementById('editUserModal').classList.remove('active');
  }
</script>
@endpush
