<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item mt-3">
      <a class="nav-link" href="#">
        <i class="menu-icon mdi mdi-file-chart"></i>
        <span class="menu-title">Laporan</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{ route('dashboard.index') }}">
        <i class="mdi mdi-view-dashboard menu-icon"></i>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>
    <li class="nav-item nav-category">Master Data</li>
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#products" aria-expanded="false" aria-controls="ui-basic">
        <i class="menu-icon mdi mdi-package-variant"></i>
        <span class="menu-title">Product</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="products">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> <a class="nav-link" href="{{ route('product.index') }}">Item List</a></li>
          <li class="nav-item"> <a class="nav-link" href="{{ route('category.index') }}">Category</a></li>
        </ul>
      </div>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#employees" aria-expanded="false" aria-controls="ui-basic">
        <i class="menu-icon mdi mdi-account-group"></i>
        <span class="menu-title">Employee</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="employees">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> <a class="nav-link" href="{{ route('employee.index') }}">Employees</a></li>
          {{-- <li class="nav-item"> <a class="nav-link" href="{{ route('role.index') }}">Role</a></li> --}}
        </ul>
      </div>
    </li>
    <li class="nav-item nav-category">Product Stock</li>
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#stock" aria-expanded="false" aria-controls="ui-basic">
        <i class="mdi mdi-cart menu-icon"></i>
        <span class="menu-title">Stock</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="stock">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> <a class="nav-link" href="{{ route('stock.index') }}">Stock</a></li>
          <li class="nav-item"> <a class="nav-link" href="{{ route('purchasing.index') }}">Purchasing</a></li>
        </ul>
      </div>
    </li>
    <li class="nav-item nav-category">Transaction</li>
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#transactions" aria-expanded="false" aria-controls="ui-basic">
        <i class="menu-icon mdi mdi-cash-register"></i>
        <span class="menu-title">Transaction</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="transactions">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> <a class="nav-link" href="{{ route('selling.index') }}">Transaction</a></li>
          <li class="nav-item"> <a class="nav-link" href="{{ route('transactionHistory.index') }}">History</a></li>

        </ul>
      </div>
    </li>
    
  </ul>
</nav>