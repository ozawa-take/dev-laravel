{{-- YouTubeUrl --}}
<div class="row m-3">
    <label class="col-sm-2 col-form-label fw-bold" for="YouTube">YouTube
        <span class="text-danger fw-bold">＊</span>
    </label>
    <div class="col-sm-10">
        <div class="input-group">
            <span class="input-group-text">https://www.youtube.com/watch?v=</span>
            <input class="form-control" type="text" id="YouTube" name="youtube_video_id" value="{{ $content->youtube_video_id ?? '' }}">
        </div>
        <p class="mb-0">（YouTubeのURL末尾のIDを挿入してください。）</p>
    </div>
</div>

{{-- 備考 --}}
<div class="row m-3" id="remarks">
    <label class="col-sm-2 col-form-label fw-bold" for="remarksArea">備考</label>
    <div class="col-sm-10">
        <textarea class="form-control" name="remarks" id="remarksArea" rows="5">{{ $content->remarks ?? '' }}</textarea>
    </div>
</div>