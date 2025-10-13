<nav class="sidebar sidebar-offcanvas" id="sidebar">
          <ul class="nav">
            <li class="nav-item mt-3">
              <a class="nav-link" href="{{ route('dashboard') }}">
                <i class="mdi mdi-view-dashboard menu-icon"></i>
                <span class="menu-title">Dashboard</span>
              </a>
            </li>
            <li class="nav-item nav-category">Master Data</li>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#products" aria-expanded="false" aria-controls="ui-basic">
                <i class="menu-icon mdi mdi-package-variant"></i>
                <span class="menu-title">Produk</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="products">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"> <a class="nav-link" href="{{ route('product.index') }}">Daftar Produk</a></li>
                  <li class="nav-item"> <a class="nav-link" href="{{ route('category.index') }}">Kategori</a></li>
                </ul>
              </div>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#employees" aria-expanded="false" aria-controls="ui-basic">
                <i class="menu-icon mdi mdi-account-group"></i>
                <span class="menu-title">Pegawai</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="employees">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"> <a class="nav-link" href="{{ route('employee.index') }}">Pegawai</a></li>
                  {{-- <li class="nav-item"> <a class="nav-link" href="{{ route('role.index') }}">Peran</a></li> --}}
                </ul>
              </div>
            </li>
            <li class="nav-item nav-category">Stock Produk</li>
            <li class="nav-item">
              <a class="nav-link" href="{{ route('stock.index') }}">
                <i class="mdi mdi-cart menu-icon"></i>
                <span class="menu-title">Stock</span>
              </a>
            </li>
            <li class="nav-item nav-category">Transaksi</li>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#transactions" aria-expanded="false" aria-controls="ui-basic">
                <i class="menu-icon mdi mdi-cash-register"></i>
                <span class="menu-title">Transaksi</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="transactions">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"> <a class="nav-link" href="{{ route('purchasing.index') }}">Pembelian</a></li>
                </ul>
              </div>
            </li>
          </ul>
        </nav>