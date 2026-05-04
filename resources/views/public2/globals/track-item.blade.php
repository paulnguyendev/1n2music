<style>
    .track-item-right{
        white-space: nowrap;
    }
    .track-sold-badge {
        background: #dc3545;
        color: white;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: bold;
        margin-left: 8px;
        text-transform: uppercase;
    }
</style>

@php
use App\Helpers\Template;
@endphp

@if(isset($item) && $item)
  @php
      $id = $item->id ?? null;
      $user = $item->user()->first() ?? null;
      $userName = rrt_get_fullname_by_user($user);
      $contracts = $item->listContracts()->get();
      $contracts = $contracts ? $contracts->toArray() : [];

      $price = Template::showTrackPrice($contracts) ?? 0;
      $trackFile = $item->file()->where('type','taggedMp3')->first() ?? null;
      if(!$trackFile) {
        $trackFile = $item->file()->where('type','unTaggedMp3')->first() ?? null;
      }
      $trackUrl = $trackFile ? url('public/uploads/tracks/' . rawurlencode($trackFile->name??"")) : null;
      $name = $item->name ?? '';
      $userId = $user->id ?? '';
      $producerLink = rrt_route('public/producers/detail', [
          'user_id' => $userId,
          'username' => $userName,
      ]);
      $bpm = $item->bpm_number ?? 0;
      $userThumbnail = $user['thumbnail'] ?? '';
      $userThumbnailUrl = $userThumbnail ? url("public/uploads/users/{$userThumbnail}") : '';
      $userThumbnailUrl = rrt_show_thumbnail($userThumbnailUrl);
      $code = $item->code ?? '';
      $isFavourite = $item->favourites()->where('user_id', $userId)->count() > 0 ? 1 : 0;
      $typePage = isset($type) ? $type : '';
  @endphp
  <div class="track-list-item" data-track="{{ $trackUrl }}" data-id="{{ $id }}" data-title="{{ $name }}"
      data-author="{{ $userName }}" data-author-url="{{ $producerLink }}" data-author-thumbnail="{{ $userThumbnailUrl }}"
      data-price="{{ $price }}" data-bpm="{{ $bpm }} BPM"
      data-download="{{ Template::checkForFreeContract($contracts) }}"
      data-url-detail="{{ rrt_route('public/track/detail', ['slug' => \Str::slug($name), 'code' => $code]) }}"
      data-contract-ids = "{{ Template::getContractsIds($contracts) }}" data-code="{{ $code }}" data-favourite = "{{$isFavourite}}"
      data-is-sold="{{ isset($item->is_sold) && $item->is_sold ? '1' : '0' }}">
      <div class="track-item-left">
          <div class="track-item-play">
              <img src="{{ asset('public/style2/img/icon_play.svg') }}" alt="">
          </div>
          <div class="track-item-info">
              <h3 class="limit-text limit-1"> {{ $item->name ?? '-' }} </h3>
              <div class="track-author">
                  <a href="#" class="limit-text limit-1">
                      {{ $userName }} </a>
              </div>
          </div>
      </div>
      <div class="track-item-right">
          <span> {{ __($price) }} </span>
          <strong> {{ $bpm }} {{__('BPM')}}</strong>
          @if(isset($item->is_sold) && $item->is_sold)
              <span class="track-sold-badge">{{ __('Sold') }}</span>
          @endif
          @if($typePage == 'history' && isset($created_at))
          <span class="track-item-create">{{ \Carbon\Carbon::parse($created_at)->format('H:i:s d/m/Y') }}</span>
          @endif
      </div>
  </div>
@endif
