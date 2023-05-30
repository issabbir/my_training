<label class="required" for="batch_id">Batch No</label>
<select class="custom-select select2" id="batch_id" name="batch_id" required>
    <option value="">--Please Select--</option>
    @if($batchNo)
        @foreach($batchNo as $batchId)
            <option value="{{$batchId->schedule_id}}">{{$batchId->batch_id}}</option>>
        @endforeach
    @endif
</select>
