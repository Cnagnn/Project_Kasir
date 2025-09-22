<nav class="sidebar sidebar-offcanvas" id="sidebar">
          <ul class="nav">
            <li class="nav-item">
              <a class="nav-link" href="{{ route('dashboard') }}">
                <i class="mdi mdi-view-dashboard menu-icon"></i>
                <span class="menu-title">Dashboard</span>
              </a>
            </li>
            <li class="nav-item nav-category">Kelola Produk</li>
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
            <li class="nav-item nav-category">Keranjang</li>
            <li class="nav-item">
              <a class="nav-link" href="{{ route('cart.index') }}">
                <i class="mdi mdi-view-dashboard menu-icon"></i>
                <span class="menu-title">Keranjang</span>
              </a>
            </li>
          </ul>
        </nav>