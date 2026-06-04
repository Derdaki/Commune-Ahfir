<div class="dropdown lang-menu">
    <button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown"><i class="bi bi-translate me-1"></i>{{ strtoupper(app()->getLocale()) }}</button>
    <ul class="dropdown-menu {{ app()->getLocale()==='ar'?'dropdown-menu-start':'dropdown-menu-end' }}">
        @foreach(['fr','en','ar'] as $locale)<li><a class="dropdown-item {{ app()->getLocale()===$locale?'active':'' }}" href="{{ route('locale.switch',$locale) }}">{{ __('app.language.'.$locale) }}</a></li>@endforeach
    </ul>
</div>
