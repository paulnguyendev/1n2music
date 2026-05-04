@extends('studio.main')
@section('page_title', $title)
@section('title', $title)
@section('buttons')
    <div class="buttons-form">
        <a href="{{ rrt_route($controllerName . '/index') }}" class="btn btn-default">{{ __('Back') }}</a>
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card_title">{{ __('Summary') }}</h4>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <tbody>
                            <tr>
                                <td style="width:150px;">{{ __('Artist') }}</td>
                                <td>{{ $item->artist ?? '' }}</td>
                            </tr>
                            <tr>
                                <td>{{ __('Title') }}</td>
                                <td>{{ $item->title ?? '' }}</td>
                            </tr>
                            <tr>
                                <td>{{ __('Album') }}</td>
                                <td>{{ $item->album ?? '' }}</td>
                            </tr>
                            <tr>
                                <td>{{ __('Release Date') }}</td>
                                <td>{{ $item->release_date ? date('d/m/Y',strtotime($item->release_date)) : '' }}</td>
                            </tr>
                            <tr>
                                <td>{{ __('Label') }}</td>
                                <td>{{ $item->label ?? '' }}</td>
                            </tr>
                            <tr>
                                <td>{{ __('Timecode') }}</td>
                                <td>{{ $item->timecode ?? '' }}</td>
                            </tr>
                            <tr>
                                <td>{{ __('Song Link') }}</td>
                                <td><a href="{{ $item->song_link }}" target="_blank">{{ $item->song_link ?? '' }}</a></td>
                            </tr>
                            <tr>
                                <td>{{ __('Created At') }}</td>
                                <td>{{ $item->created_at ? date('d/m/Y',strtotime($item->created_at)) : '' }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @if( in_array('apple_music',$item->platforms) || in_array('all',$item->platforms)  )
                <div class="card mt-3">
                    <div class="card-body">
                        <h4 class="card_title">{{ __('Apple Music') }}</h4>
                        @if($item->apple_music)
                            @php
                                $platform_data = json_decode($item->apple_music,true);
                            @endphp
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <tbody>
                                    <tr>
                                        <td style="width:150px;">{{ __('Artist Name') }}</td>
                                        <td>{{ $platform_data['artistName'] ?? __('N/A') }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Song Name') }}</td>
                                        <td>{{ $platform_data['name'] ?? __('N/A') }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Album Name') }}</td>
                                        <td>{{ $platform_data['albumName'] ?? __('N/A') }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Release Date') }}</td>
                                        <td>{{ $platform_data['releaseDate'] ? \Carbon\Carbon::parse($platform_data['releaseDate'])->format('d/m/Y') : __('N/A') }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Duration') }}</td>
                                        <td>{{ isset($platform_data['durationInMillis']) ? rrt_convert_duration($platform_data['durationInMillis']) : __('N/A') }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Track Number') }}</td>
                                        <td>{{ $platform_data['trackNumber'] ?? __('N/A') }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Disc Number') }}</td>
                                        <td>{{ $platform_data['discNumber'] ?? __('N/A') }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('ISRC') }}</td>
                                        <td>{{ $platform_data['isrc'] ?? __('N/A') }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Composer Name') }}</td>
                                        <td>{{ $platform_data['composerName'] ?? __('N/A') }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Genres') }}</td>
                                        <td>{{ isset($platform_data['genreNames']) ? implode(', ', $platform_data['genreNames']) : __('N/A') }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Artwork URL') }}</td>
                                        <td>
                                            @php
                                                $artworkUrl = $platform_data['artwork']['url'] ?? null;
                                                if ($artworkUrl) {
                                                    $artworkUrl = str_replace(['{w}', '{h}'], [200, 200], $artworkUrl);
                                                }
                                            @endphp
                                            @if($artworkUrl)
                                                <img src="{{ $artworkUrl }}" alt="{{ __('Artwork') }}" style="max-width: 200px">
                                            @else
                                                {{ __('N/A') }}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Preview URL') }}</td>
                                        <td>
                                            @if(isset($platform_data['previews'][0]['url']))
                                                <audio controls>
                                                    <source src="{{ $platform_data['previews'][0]['url'] }}" type="audio/mp3">
                                                    {{ __('Your browser does not support the audio tag.') }}
                                                </audio>
                                            @else
                                                {{ __('N/A') }}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Apple Digital Master') }}</td>
                                        <td>{{ $platform_data['isAppleDigitalMaster'] ? __('Yes') : __('No') }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Has Lyrics') }}</td>
                                        <td>{{ $platform_data['hasLyrics'] ? __('Yes') : __('No') }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('URL') }}</td>
                                        <td><a href="{{ $platform_data['url'] ?? '#' }}" target="_blank">{{ $platform_data['url'] ?? __('N/A') }}</a></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-center">{{ __('This Audio is proprietary') }}</p>
                        @endif
                    </div>
                </div>
            @endif

            @if( in_array('spotify',$item->platforms) || in_array('all',$item->platforms)  )
                <div class="card mt-3">
                    <div class="card-body">
                        <h4 class="card_title">{{ __('Spotify') }}</h4>
                        @if($item->spotify)
                            @php
                                $platform_data = json_decode($item->spotify,true);
                            @endphp
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <tbody>
                                    <tr>
                                        <td style="width:150px;">{{ __('Artist Name') }}</td>
                                        <td>
                                            @if(isset($platform_data['artists']) && count($platform_data['artists']) > 0)
                                                {{ implode(', ', array_column($platform_data['artists'], 'name')) }}
                                            @else
                                                {{ __('N/A') }}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Song Name') }}</td>
                                        <td>{{ $platform_data['name'] ?? __('N/A') }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Album Name') }}</td>
                                        <td>{{ $platform_data['name'] ?? __('N/A') }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Release Date') }}</td>
                                        <td>{{ isset($platform_data['release_date']) ? \Carbon\Carbon::parse($platform_data['release_date'])->format('d/m/Y') : __('N/A') }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Duration') }}</td>
                                        <td>{{ isset($platform_data['duration_ms']) ? rrt_convert_duration($platform_data['duration_ms']) : __('N/A') }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Track Number') }}</td>
                                        <td>{{ $platform_data['track_number'] ?? __('N/A') }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Disc Number') }}</td>
                                        <td>{{ $platform_data['disc_number'] ?? __('N/A') }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('ISRC') }}</td>
                                        <td>{{ $platform_data['external_ids']['isrc'] ?? __('N/A') }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Artwork URL') }}</td>
                                        <td>
                                            @if(isset($platform_data['images']) && count($platform_data['images']) > 0)
                                                <img src="{{ $platform_data['images'][0]['url'] }}" alt="{{ __('Artwork') }}" style="width: 100px; height: auto;">
                                            @else
                                                {{ __('N/A') }}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('URL') }}</td>
                                        <td>
                                            <a href="{{ $platform_data['external_urls']['spotify'] ?? '#' }}" target="_blank">{{ $platform_data['external_urls']['spotify'] ?? __('N/A') }}</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Popularity') }}</td>
                                        <td>{{ $platform_data['popularity'] ?? __('N/A') }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Available Markets') }}</td>
                                        <td>{{ isset($platform_data['available_markets']) ? implode(', ', $platform_data['available_markets']) : __('N/A') }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-center">{{ __('This Audio is proprietary') }}</p>
                        @endif
                    </div>
                </div>
            @endif

            @if( in_array('deezer',$item->platforms) || in_array('all',$item->platforms)  )
                <div class="card mt-3">
                    <div class="card-body">
                        <h4 class="card_title">{{ __('Deezer') }}</h4>
                        @if($item->deezer)
                            @php
                                $platform_data = json_decode($item->deezer,true);
                            @endphp
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <tr style="width:150px;">
                                        <td>{{ __('Song ID') }}</td>
                                        <td>{{ $platform_data['id'] ?? __('N/A') }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Song Title') }}</td>
                                        <td>{{ $platform_data['title'] ?? __('N/A') }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('ISRC') }}</td>
                                        <td>{{ $platform_data['isrc'] ?? __('N/A') }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Link') }}</td>
                                        <td><a href="{{ $platform_data['link'] ?? '#' }}">{{ __('Listen on Deezer') }}</a></td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Duration') }}</td>
                                        <td>{{ $platform_data['duration'] ?? __('N/A') }} {{ __('seconds') }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Track Position') }}</td>
                                        <td>{{ $platform_data['track_position'] ?? __('N/A') }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Release Date') }}</td>
                                        <td>{{ $platform_data['release_date'] ?? __('N/A') }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Explicit Lyrics') }}</td>
                                        <td>{{ $platform_data['explicit_lyrics'] ? __('Yes') : __('No') }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('BPM') }}</td>
                                        <td>{{ $platform_data['bpm'] ?? __('N/A') }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Album Cover') }}</td>
                                        <td><img src="{{ $platform_data['album']['cover'] ?? '#' }}" alt="{{ __('Album Cover') }}" width="100"></td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Main Artist') }}</td>
                                        <td><a href="{{ $platform_data['artist']['link'] ?? '#' }}">{{ $platform_data['artist']['name'] ?? __('N/A') }}</a></td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Featured Artist') }}</td>
                                        <td>
                                            @foreach ($platform_data['contributors'] as $contributor)
                                                <a href="{{ $contributor['link'] }}">{{ $contributor['name'] }}</a>@if (!$loop->last), @endif
                                            @endforeach
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Album') }}</td>
                                        <td><a href="{{ $platform_data['album']['link'] ?? '#' }}">{{ $platform_data['album']['title'] ?? __('N/A') }}</a></td>
                                    </tr>
                                </table>
                            </div>
                        @else
                            <p class="text-center">{{ __('This Audio is proprietary') }}</p>
                        @endif
                    </div>
                </div>
            @endif

        @if( in_array('musicbrainz',$item->platforms) || in_array('all',$item->platforms)  )
        <div class="card mt-3">
            <div class="card-body">
                <h4 class="card_title">{{ __('Musicbrainz') }}</h4>
                @if($item->musicbrainz)
                    @php
                        $platform_data = json_decode($item->musicbrainz,true);
                        $platform_data = isset($platform_data[0]) ? $platform_data[0] : null;
                    @endphp
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <tr>
                                <th>Title</th>
                                <td>{{ $platform_data['title'] ?? __('N/A') }}</td>
                            </tr>
                            <tr>
                                <th>Length</th>
                                <td>{{ $platform_data['length'] ?? __('N/A') }}</td>
                            </tr>
                            <tr>
                                <th>Length</th>
                                <td>{{ $platform_data['isrcs'][0] ?? __('N/A') }}</td>
                            </tr>
                        </table>

                    </div>
                @else
                    <p class="text-center">This Audio is proprietary</p>
                @endif
            </div>
        </div>
        @endif

        @if( in_array('napster',$item->platforms) || in_array('all',$item->platforms)  )
        <div class="card mt-3">
            <div class="card-body">
                <h4 class="card_title">{{ __('Napster') }}</h4>
                @if($item->napster)
                @php
                    $platform_data = json_decode($item->napster,true);
                @endphp
                <div class="table-responsive">
                    <table class="table table-hover">
                        <tbody>
                            @if (!empty($platform_data['id']))
                                <tr>
                                    <td>ID</td>
                                    <td>{{ $platform_data['id'] }}</td>
                                </tr>
                            @endif

                            @if (!empty($platform_data['name']))
                                <tr>
                                    <td>Name</td>
                                    <td>{{ $platform_data['name'] }}</td>
                                </tr>
                            @endif

                            @if (!empty($platform_data['artistName']))
                                <tr>
                                    <td>Artist Name</td>
                                    <td>{{ $platform_data['artistName'] }}</td>
                                </tr>
                            @endif

                            @if (!empty($platform_data['albumName']))
                                <tr>
                                    <td>Album Name</td>
                                    <td>{{ $platform_data['albumName'] }}</td>
                                </tr>
                            @endif

                            @if (!empty($platform_data['playbackSeconds']))
                                <tr>
                                    <td>Playback Seconds</td>
                                    <td>{{ $platform_data['playbackSeconds'] }}</td>
                                </tr>
                            @endif

                            @if (!empty($platform_data['isExplicit']))
                                <tr>
                                    <td>Explicit Content</td>
                                    <td>{{ $platform_data['isExplicit'] ? 'Yes' : 'No' }}</td>
                                </tr>
                            @endif

                            @if (!empty($platform_data['isStreamable']))
                                <tr>
                                    <td>Streamable</td>
                                    <td>{{ $platform_data['isStreamable'] ? 'Yes' : 'No' }}</td>
                                </tr>
                            @endif

                            @if (!empty($platform_data['isAvailableInHiRes']))
                                <tr>
                                    <td>Available in Hi-Res</td>
                                    <td>{{ $platform_data['isAvailableInHiRes'] ? 'Yes' : 'No' }}</td>
                                </tr>
                            @endif

                            @if (!empty($platform_data['isrc']))
                                <tr>
                                    <td>ISRC</td>
                                    <td>{{ $platform_data['isrc'] }}</td>
                                </tr>
                            @endif

                            @if (!empty($platform_data['previewURL']))
                                <tr>
                                    <td>Preview URL</td>
                                    <td><a href="{{ $platform_data['previewURL'] }}" target="_blank">Preview</a></td>
                                </tr>
                            @endif

                            <!-- Formats -->
                            @if (!empty($platform_data['formats']) && is_array($platform_data['formats']))
                                <tr>
                                    <td>Formats</td>
                                    <td>
                                        <ul>
                                            @foreach ($platform_data['formats'] as $format)
                                                <li>{{ $format['name'] }} ({{ $format['bitrate'] }} kbps)</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                </tr>
                            @endif

                            <!-- Lossless Formats -->
                            @if (!empty($platform_data['losslessFormats']) && is_array($platform_data['losslessFormats']))
                                <tr>
                                    <td>Lossless Formats</td>
                                    <td>
                                        <ul>
                                            @foreach ($platform_data['losslessFormats'] as $losslessFormat)
                                                <li>{{ $losslessFormat['name'] }} ({{ $losslessFormat['bitrate'] }} kbps)</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                @else
                    <p class="text-center">This Audio is proprietary</p>
                @endif
            </div>
        </div>
        @endif

        </div>
    </div>

@endsection
