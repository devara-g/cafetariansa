@extends('admin.layout')

@section('title', 'Kelola Menu Makanan & Minuman')
@section('page_title', 'Daftar Menu Kantin')
@section('page_subtitle', 'Tambah, edit, hapus, dan atur ketersediaan menu makanan & minuman kantin')

@section('content')

  <!-- 1. ACTIONS & FILTER BAR -->
  <div class="card-custom" style="padding:18px 22px; margin-bottom:24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
      
      <!-- Filter and search form -->
      <form action="{{ route('admin.menus.index') }}" method="GET" style="display:flex; gap:10px; flex-wrap:wrap; flex:1;">
        <div style="position:relative; min-width:220px; flex:1;">
          <ion-icon name="search-outline" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); font-size:1.8rem; color:#94a3b8;"></ion-icon>
          <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama menu..." class="form-control-admin" style="padding-left:38px;">
        </div>

        <select name="stand_id" class="form-control-admin" style="width:180px;">
          <option value="">Semua Stand</option>
          @foreach($stands as $std)
            <option value="{{ $std->id }}" {{ $standId == $std->id ? 'selected' : '' }}>{{ $std->name }}</option>
          @endforeach
        </select>

        <select name="category_id" class="form-control-admin" style="width:180px;">
          <option value="">Semua Kategori</option>
          @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
          @endforeach
        </select>

        <button type="submit" class="btn-action btn-secondary" style="padding:10px 16px;">
          <ion-icon name="filter-outline"></ion-icon>
          <span>Filter</span>
        </button>

        @if(!empty($search) || !empty($standId) || !empty($categoryId))
          <a href="{{ route('admin.menus.index') }}" class="btn-action btn-secondary" style="padding:10px 14px;">
            <span>Reset</span>
          </a>
        @endif
      </form>

      <!-- Add Menu Button -->
      <button type="button" class="btn-action btn-primary" onclick="openAddMenuModal()">
        <ion-icon name="add-circle-outline"></ion-icon>
        <span>Tambah Menu Baru</span>
      </button>

    </div>
  </div>

  <!-- 2. MENUS TABLE -->
  <div class="card-custom">
    <div class="table-container">
      <table class="table-custom">
        <thead>
          <tr>
            <th style="width:70px;">Foto</th>
            <th>Nama Menu</th>
            <th>Kategori & Stand</th>
            <th>Harga Jual</th>
            <th>Ketersediaan</th>
            <th>Badges</th>
            <th style="text-align:right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($menus as $menu)
            <tr>
              <!-- Foto -->
              <td>
                <img src="{{ asset($menu->image ?? 'assets/images/food-menu-1.png') }}" 
                     alt="{{ $menu->name }}" 
                     style="width:40px; height:40px; border-radius:8px; object-fit:cover; border:1px solid #e2e8f0;">
              </td>

              <!-- Nama Menu -->
              <td>
                <div style="font-weight:600; font-size:13.5px; color:var(--text);">{{ $menu->name }}</div>
                @if($menu->description)
                  <div style="font-size:11.5px; color:var(--text-muted); max-width:280px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                    {{ $menu->description }}
                  </div>
                @endif
                <div style="font-size:10.5px; color:#94a3b8; font-family:'JetBrains Mono', monospace;">slug: {{ $menu->slug }}</div>
              </td>

              <!-- Kategori & Stand -->
              <td>
                <div style="font-weight:600; font-size:12.5px; color:var(--text);">{{ $menu->category->name ?? 'Kategori' }}</div>
                <div style="font-size:11.5px; color:var(--primary); font-weight:500;">
                  <ion-icon name="storefront-outline" style="vertical-align:middle;"></ion-icon>
                  <span>{{ $menu->stand->name ?? 'Stand Kantin' }}</span>
                </div>
              </td>

              <!-- Harga -->
              <td>
                <div style="font-weight:700; font-size:13.5px; color:var(--text);">Rp {{ number_format($menu->price, 0, ',', '.') }}</div>
                @if($menu->cost_price)
                  <div style="font-size:11px; color:var(--text-muted);">Modal: Rp {{ number_format($menu->cost_price, 0, ',', '.') }}</div>
                @endif
              </td>

              <!-- Ketersediaan Toggle -->
              <td>
                <form action="{{ route('admin.menus.toggle-available', $menu->id) }}" method="POST">
                  @csrf
                  @if($menu->is_available)
                    <button type="submit" class="badge-status success" style="border:none; cursor:pointer;" title="Klik untuk ubah jadi HABIS">
                      <ion-icon name="checkmark-circle"></ion-icon>
                      <span>Tersedia</span>
                    </button>
                  @else
                    <button type="submit" class="badge-status danger" style="border:none; cursor:pointer;" title="Klik untuk ubah jadi TERSEDIA">
                      <ion-icon name="close-circle"></ion-icon>
                      <span>Habis</span>
                    </button>
                  @endif
                </form>
              </td>

              <!-- Badges Toggle -->
              <td>
                <div style="display:flex; gap:4px;">
                  <!-- Popular -->
                  <form action="{{ route('admin.menus.toggle-badge', ['menu' => $menu->id, 'badgeType' => 'popular']) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" style="border:none; background:none; cursor:pointer;" title="Toggle Favorit">
                      <span class="badge-status {{ $menu->is_popular ? 'warning' : 'gray' }}" style="font-size:10.5px; padding:2px 6px;">
                        ★ Favorit
                      </span>
                    </button>
                  </form>

                  <!-- Recommended -->
                  <form action="{{ route('admin.menus.toggle-badge', ['menu' => $menu->id, 'badgeType' => 'recommended']) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" style="border:none; background:none; cursor:pointer;" title="Toggle Rekomendasi">
                      <span class="badge-status {{ $menu->is_recommended ? 'info' : 'gray' }}" style="font-size:10.5px; padding:2px 6px;">
                        ✓ Rekomendasi
                      </span>
                    </button>
                  </form>
                </div>
              </td>

              <!-- Aksi Edit & Delete -->
              <td style="text-align:right;">
                <div style="display:inline-flex; gap:6px;">
                  <button type="button" class="btn-action btn-secondary btn-sm" data-menu="{{ json_encode($menu) }}" onclick="openEditMenuModal(this)" title="Edit Menu">
                    <ion-icon name="create-outline"></ion-icon>
                  </button>

                  <form action="{{ route('admin.menus.destroy', $menu->id) }}" method="POST" data-confirm="Hapus menu {{ $menu->name }}?" onsubmit="return confirm(this.dataset.confirm);" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-action btn-secondary btn-sm" style="color:var(--rose);" title="Hapus Menu">
                      <ion-icon name="trash-outline"></ion-icon>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" style="text-align:center; padding:35px; color:#64748b;">
                Belum ada menu yang terdaftar atau cocok dengan filter.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div style="margin-top:20px;">
      {{ $menus->links() }}
    </div>
  </div>

  <!-- 3. MODAL TAMBAH MENU -->
  <div class="modal-backdrop-custom" id="addMenuModal" onclick="if(event.target===this) closeAddMenuModal()">
    <div class="modal-card-custom">
      <div class="modal-header-custom">
        <h3>Tambah Menu Kantin Baru</h3>
        <button class="modal-close-btn" onclick="closeAddMenuModal()">&times;</button>
      </div>

      <form action="{{ route('admin.menus.store') }}" method="POST">
        @csrf
        <div class="modal-body-custom">
          
          <div class="form-group-admin">
            <label class="form-label-admin">Nama Menu *</label>
            <input type="text" name="name" class="form-control-admin" placeholder="Contoh: Ayam Geprek Sambal Bawang" required>
          </div>

          <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
            <div class="form-group-admin">
              <label class="form-label-admin">Stand Penjual *</label>
              <select name="stand_id" class="form-control-admin" required>
                @foreach($stands as $std)
                  <option value="{{ $std->id }}">{{ $std->name }}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group-admin">
              <label class="form-label-admin">Kategori *</label>
              <select name="category_id" class="form-control-admin" required>
                @foreach($categories as $cat)
                  <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
            <div class="form-group-admin">
              <label class="form-label-admin">Harga Jual (Rp) *</label>
              <input type="number" name="price" class="form-control-admin" placeholder="Contoh: 12000" min="500" required>
            </div>

            <div class="form-group-admin">
              <label class="form-label-admin">Harga Modal (Rp)</label>
              <input type="number" name="cost_price" class="form-control-admin" placeholder="Contoh: 9000" min="0">
            </div>
          </div>

          <div class="form-group-admin">
            <label class="form-label-admin">URL / Path Foto Menu</label>
            <input type="text" name="image" class="form-control-admin" placeholder="Contoh: assets/images/food-menu-1.png">
          </div>

          <div class="form-group-admin">
            <label class="form-label-admin">Deskripsi Menu (Opsional)</label>
            <textarea name="description" class="form-control-admin" rows="2" placeholder="Komposisi, rasa, atau catatan sajian"></textarea>
          </div>

          <div style="display:flex; gap:18px; margin-top:12px; font-size:1.3rem;">
            <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
              <input type="checkbox" name="is_available" value="1" checked>
              <span>Menu Tersedia</span>
            </label>
            <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
              <input type="checkbox" name="is_popular" value="1">
              <span>Tandai Favorit</span>
            </label>
            <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
              <input type="checkbox" name="is_recommended" value="1">
              <span>Tandai Rekomendasi</span>
            </label>
          </div>

        </div>

        <div class="modal-footer-custom">
          <button type="button" class="btn-action btn-secondary" onclick="closeAddMenuModal()">Batal</button>
          <button type="submit" class="btn-action btn-primary">Simpan Menu Baru</button>
        </div>
      </form>
    </div>
  </div>

  <!-- 4. MODAL EDIT MENU -->
  <div class="modal-backdrop-custom" id="editMenuModal" onclick="if(event.target===this) closeEditMenuModal()">
    <div class="modal-card-custom">
      <div class="modal-header-custom">
        <h3>Edit Data Menu</h3>
        <button class="modal-close-btn" onclick="closeEditMenuModal()">&times;</button>
      </div>

      <form id="editMenuForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body-custom">
          
          <div class="form-group-admin">
            <label class="form-label-admin">Nama Menu *</label>
            <input type="text" name="name" id="edit_name" class="form-control-admin" required>
          </div>

          <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
            <div class="form-group-admin">
              <label class="form-label-admin">Stand Penjual *</label>
              <select name="stand_id" id="edit_stand_id" class="form-control-admin" required>
                @foreach($stands as $std)
                  <option value="{{ $std->id }}">{{ $std->name }}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group-admin">
              <label class="form-label-admin">Kategori *</label>
              <select name="category_id" id="edit_category_id" class="form-control-admin" required>
                @foreach($categories as $cat)
                  <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
            <div class="form-group-admin">
              <label class="form-label-admin">Harga Jual (Rp) *</label>
              <input type="number" name="price" id="edit_price" class="form-control-admin" min="500" required>
            </div>

            <div class="form-group-admin">
              <label class="form-label-admin">Harga Modal (Rp)</label>
              <input type="number" name="cost_price" id="edit_cost_price" class="form-control-admin" min="0">
            </div>
          </div>

          <div class="form-group-admin">
            <label class="form-label-admin">URL / Path Foto Menu</label>
            <input type="text" name="image" id="edit_image" class="form-control-admin">
          </div>

          <div class="form-group-admin">
            <label class="form-label-admin">Deskripsi Menu</label>
            <textarea name="description" id="edit_description" class="form-control-admin" rows="2"></textarea>
          </div>

          <div style="display:flex; gap:18px; margin-top:12px; font-size:1.3rem;">
            <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
              <input type="checkbox" name="is_available" id="edit_is_available" value="1">
              <span>Menu Tersedia</span>
            </label>
            <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
              <input type="checkbox" name="is_popular" id="edit_is_popular" value="1">
              <span>Favorit</span>
            </label>
            <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
              <input type="checkbox" name="is_recommended" id="edit_is_recommended" value="1">
              <span>Rekomendasi</span>
            </label>
          </div>

        </div>

        <div class="modal-footer-custom">
          <button type="button" class="btn-action btn-secondary" onclick="closeEditMenuModal()">Batal</button>
          <button type="submit" class="btn-action btn-primary">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>

@endsection

@push('scripts')
<script>
  function openAddMenuModal() {
    document.getElementById('addMenuModal').classList.add('active');
  }
  function closeAddMenuModal() {
    document.getElementById('addMenuModal').classList.remove('active');
  }

  function openEditMenuModal(source) {
    const menu = (source && source.dataset && source.dataset.menu) ? JSON.parse(source.dataset.menu) : source;
    document.getElementById('editMenuForm').action = `/admin/menus/${menu.id}`;
    document.getElementById('edit_name').value = menu.name;
    document.getElementById('edit_stand_id').value = menu.stand_id;
    document.getElementById('edit_category_id').value = menu.category_id;
    document.getElementById('edit_price').value = menu.price;
    document.getElementById('edit_cost_price').value = menu.cost_price || '';
    document.getElementById('edit_image').value = menu.image || '';
    document.getElementById('edit_description').value = menu.description || '';
    document.getElementById('edit_is_available').checked = Boolean(menu.is_available);
    document.getElementById('edit_is_popular').checked = Boolean(menu.is_popular);
    document.getElementById('edit_is_recommended').checked = Boolean(menu.is_recommended);

    document.getElementById('editMenuModal').classList.add('active');
  }
  function closeEditMenuModal() {
    document.getElementById('editMenuModal').classList.remove('active');
  }
</script>
@endpush
