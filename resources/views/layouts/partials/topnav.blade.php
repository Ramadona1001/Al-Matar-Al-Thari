<div class="top-navbar d-none d-md-block">
    <div class="container">
        <div class="row align-items-center">
            <!-- Left Side - Contact Info -->
            <div class="col-md-6 contact-info">
                <div class="d-flex flex-wrap gap-3">
                    @if ($site->contact_email)
                        <a href="mailto:{{ $site->contact_email }}" aria-label="Send email to {{ $site->contact_email }}">
                            <i class="bi bi-envelope" aria-hidden="true"></i>
                            <span>{{ $site->contact_email }}</span>
                        </a>
                    @endif
                    @if ($site->contact_phone)
                        <a href="tel:{{ $site->contact_phone }}" aria-label="Call {{ $site->contact_phone }}">
                            <i class="bi bi-telephone" aria-hidden="true"></i>
                            <span>{{ $site->contact_phone }}</span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Right Side - Social Media -->
            @if (isset($socialLinks) && is_array($socialLinks) && count($socialLinks) > 0)
                <div class="col-md-6 social-icons text-end">
                    @foreach ($socialLinks as $platform => $url)
                        @if ($url)
                            <a href="{{ $url }}" target="_blank"
                                aria-label="Visit our {{ ucfirst($platform) }} page">
                                <i class="fab fa-{{ $platform }}" aria-hidden="true"></i>
                            </a>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
