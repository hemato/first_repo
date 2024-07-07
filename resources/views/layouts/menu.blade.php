<!-- need to remove -->
<li class="nav-item">
    <a href="{{ route('home') }}" class="nav-link {{ Request::is('home') ? 'active' : '' }}">
        <i class="nav-icon fas fa-home"></i>
        <p>Home</p>
    </a>
    <a href="{{ route('price-comparisons2') }}" class="nav-link {{ Request::is('price-comparisons2') ? 'active' : '' }}">
        <i class="nav-icon fas fa-home"></i>
        <p>Buy Max - Sell Min</p>
    </a>
    <a href="{{ route('price-comparisons3') }}" class="nav-link {{ Request::is('price-comparisons3') ? 'active' : '' }}">
        <i class="nav-icon fas fa-home"></i>
        <p>Buy Max - Buy Max</p>
    </a>
    <a href="{{ route('price-comparisons4') }}" class="nav-link {{ Request::is('price-comparisons4') ? 'active' : '' }}">
        <i class="nav-icon fas fa-home"></i>
        <p>Sell Min - Sell Min</p>
    </a>
    <a href="{{ route('price-comparisons1') }}" class="nav-link {{ Request::is('price-comparisons1') ? 'active' : '' }}">
        <i class="nav-icon fas fa-home"></i>
        <p>Sell Min - Buy Max</p>
    </a>
</li>
