@extends('admin.layout')

@section('title', 'Kelola Kategori Menu')
@section('page_title', 'Kategori Makanan & Minuman')
@section('page_subtitle', 'Kelola klasifikasi menu kantin, urutan tampilan di katalog, dan status aktif')

@section('content')

  <!-- 1. ACTION BAR -->
  <div class="card-custom" style="padding:18px 22px; margin-bottom:24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
      <div>
        <h3 style="font-size:1.6rem; font-weight:800; color:var(--dark);">Daftar Kategori Produk</h3>
        <p style="font-size:1.25rem; color:var(--text-muted);">Total {{ $categories->count() }} kategori terdaftar di sistem Kantin</p>
      </div>

      <button type="button" class="btn-action btn-primary" onclick="openAddCategoryModal()">
        <ion-icon name="add-circle-outline"></ion-icon>
        <span>Tambah Kategori Baru</span>
      </button>
    </div>
  </div>

  <!-- 2. CATEGORIES TABLE -->
  <div class="card-custom">
    <div class="table-container">
      <table class="table-custom">
        <thead>
          <tr>
            <th style="width:70px;">Urutan</th>
            <th style="width:70px;">Ikon</th>
            <th>Nama Kategori</th>
            <th>Slug</th>
            <th>Deskripsi</th>
            <th>Jumlah Menu</th>
            <th>Status</th>
            <th style="text-align:right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($categories as $category)
            <tr>
              <!-- Urutan -->
              <td>
                <span style="font-weight:600; font-family:'JetBrains Mono', monospace; font-size:12px; color:#64748b;">
                  #{{ $category->sort_order ?? 0 }}
                </span>
              </td>

              <!-- Ikon -->
              <td>
                <div style="width:32px; height:32px; border-radius:6px; background:var(--primary-soft); color:var(--primary); display:flex; align-items:center; justify-content:center; font-size:16px;">
                  @if($category->icon)
                    <ion-icon name="{{ $category->icon }}"></ion-icon>
                  @else
                    <ion-icon name="pricetag-outline"></ion-icon>
                  @endif
                </div>
              </td>

              <!-- Nama Kategori -->
              <td>
                <div style="font-weight:600; font-size:13.5px; color:var(--text);">{{ $category->name }}</div>
              </td>

              <!-- Slug -->
              <td>
                <span style="font-size:11px; color:#64748b; font-family:'JetBrains Mono', monospace; background:#f1f5f9; padding:2px 6px; border-radius:4px;">
                  {{ $category->slug }}
                </span>
              </td>

              <!-- Deskripsi -->
              <td>
                <div style="font-size:12px; color:var(--text-muted); max-width:320px;">
                  {{ $category->description ?? '-' }}
                </div>
              </td>

              <!-- Jumlah Menu -->
              <td>
                <a href="{{ route('admin.menus.index', ['category_id' => $category->id]) }}" class="badge-status gray" style="text-decoration:none; font-size:11.5px; cursor:pointer;" title="Lihat menu kategori ini">
                  <ion-icon name="restaurant-outline"></ion-icon>
                  <span>{{ $category->menus_count }} Menu</span>
                </a>
              </td>

              <!-- Status Aktif -->
              <td>
                @if($category->is_active)
                  <span class="badge-status success">
                    <ion-icon name="checkmark-circle"></ion-icon>
                    <span>Aktif</span>
                  </span>
                @else
                  <span class="badge-status gray">
                    <ion-icon name="pause-circle"></ion-icon>
                    <span>Nonaktif</span>
                  </span>
                @endif
              </td>

              <!-- Aksi -->
              <td style="text-align:right;">
                <div style="display:inline-flex; gap:6px;">
                  <button type="button" class="btn-action btn-secondary btn-sm" data-category="{{ json_encode($category) }}" onclick="openEditCategoryModal(this)" title="Edit Kategori">
                    <ion-icon name="create-outline"></ion-icon>
                  </button>

                  <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" data-confirm="Hapus kategori {{ $category->name }}? Menu yang terkait mungkin kehilangan kategori." onsubmit="return confirm(this.dataset.confirm);" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-action btn-secondary btn-sm" style="color:var(--rose);" title="Hapus Kategori">
                      <ion-icon name="trash-outline"></ion-icon>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" style="text-align:center; padding:35px; color:#64748b;">
                Belum ada kategori yang dibuat.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- 3. MODAL TAMBAH KATEGORI -->
  <div class="modal-backdrop-custom" id="addCategoryModal" onclick="if(event.target===this) closeAddCategoryModal()">
    <div class="modal-card-custom">
      <div class="modal-header-custom">
        <h3>Tambah Kategori Baru</h3>
        <button class="modal-close-btn" onclick="closeAddCategoryModal()">&times;</button>
      </div>

      <form action="{{ route('admin.categories.store') }}" method="POST">
        @csrf
        <div class="modal-body-custom">
          
          <div class="form-group-admin">
            <label class="form-label-admin">Nama Kategori *</label>
            <input type="text" name="name" class="form-control-admin" placeholder="Contoh: Aneka Jus & Minuman Dingin" required>
          </div>

          <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
            <div class="form-group-admin">
              <label class="form-label-admin">Ikon Ionicon</label>
              <input type="text" name="icon" class="form-control-admin" placeholder="water-outline / fast-food-outline">
            </div>

            <div class="form-group-admin">
              <label class="form-label-admin">Urutan Tampil (Sort Order)</label>
              <input type="number" name="sort_order" class="form-control-admin" placeholder="1" min="0">
            </div>
          </div>

          <div class="form-group-admin">
            <label class="form-label-admin">Deskripsi Kategori (Opsional)</label>
            <textarea name="description" class="form-control-admin" rows="2" placeholder="Kategori minuman pelepas dahaga"></textarea>
          </div>

          <div style="margin-top:10px;">
            <label style="display:flex; align-items:center; gap:8px; font-size:1.35rem; cursor:pointer;">
              <input type="checkbox" name="is_active" value="1" checked>
              <span>Kategori Aktif & Tampil di Katalog Publik</span>
            </label>
          </div>

        </div>

        <div class="modal-footer-custom">
          <button type="button" class="btn-action btn-secondary" onclick="closeAddCategoryModal()">Batal</button>
          <button type="submit" class="btn-action btn-primary">Simpan Kategori</button>
        </div>
      </form>
    </div>
  </div>

  <!-- 4. MODAL EDIT KATEGORI -->
  <div class="modal-backdrop-custom" id="editCategoryModal" onclick="if(event.target===this) closeEditCategoryModal()">
    <div class="modal-card-custom">
      <div class="modal-header-custom">
        <h3>Edit Kategori</h3>
        <button class="modal-close-btn" onclick="closeEditCategoryModal()">&times;</button>
      </div>

      <form id="editCategoryForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body-custom">
          
          <div class="form-group-admin">
            <label class="form-label-admin">Nama Kategori *</label>
            <input type="text" name="name" id="edit_name" class="form-control-admin" required>
          </div>

          <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
            <div class="form-group-admin">
              <label class="form-label-admin">Ikon Ionicon</label>
              <input type="text" name="icon" id="edit_icon" class="form-control-admin">
            </div>

            <div class="form-group-admin">
              <label class="form-label-admin">Urutan Tampil (Sort Order)</label>
              <input type="number" name="sort_order" id="edit_sort_order" class="form-control-admin" min="0">
            </div>
          </div>

          <div class="form-group-admin">
            <label class="form-label-admin">Deskripsi Kategori</label>
            <textarea name="description" id="edit_description" class="form-control-admin" rows="2"></textarea>
          </div>

          <div style="margin-top:10px;">
            <label style="display:flex; align-items:center; gap:8px; font-size:1.35rem; cursor:pointer;">
              <input type="checkbox" name="is_active" id="edit_is_active" value="1">
              <span>Kategori Aktif</span>
            </label>
          </div>

        </div>

        <div class="modal-footer-custom">
          <button type="button" class="btn-action btn-secondary" onclick="closeEditCategoryModal()">Batal</button>
          <button type="submit" class="btn-action btn-primary">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>

@endsection

@push('scripts')
<script>
  function openAddCategoryModal() {
    document.getElementById('addCategoryModal').classList.add('active');
  }
  function closeAddCategoryModal() {
    document.getElementById('addCategoryModal').classList.remove('active');
  }

  function openEditCategoryModal(source) {
    const category = (source && source.dataset && source.dataset.category) ? JSON.parse(source.dataset.category) : source;
    document.getElementById('editCategoryForm').action = `/admin/categories/${category.id}`;
    document.getElementById('edit_name').value = category.name;
    document.getElementById('edit_icon').value = category.icon || '';
    document.getElementById('edit_sort_order').value = category.sort_order ?? 0;
    document.getElementById('edit_description').value = category.description || '';
    document.getElementById('edit_is_active').checked = Boolean(category.is_active);

    document.getElementById('editCategoryModal').classList.add('active');
  }
  function closeEditCategoryModal() {
    document.getElementById('editCategoryModal').classList.remove('active');
  }
</script>
@endpush
