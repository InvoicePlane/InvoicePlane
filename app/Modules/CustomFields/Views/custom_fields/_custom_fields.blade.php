<script type="text/javascript">
    $(function () {
        autosize($('textarea.custom-form-field'));
    });
</script>

@foreach ($customFields as $customField)
    <div class="form-group">
        <label>{{ $customField->field_label }}</label>
        @if ($customField->field_type == 'dropdown')
            {!! Form::select('custom[' . $customField->column_name . ']', array_combine(array_merge([''], explode(',', $customField->field_meta)), array_merge([''], explode(',', $customField->field_meta))), null, ['class' => 'custom-form-field form-control', 'data-' . $customField->tbl_name . '-field-name' => $customField->column_name]) !!}
        @else
            {!! call_user_func_array('Form::' . $customField->field_type, ['custom[' . $customField->column_name . ']', null, ['class' => 'custom-form-field form-control', 'data-' . $customField->tbl_name . '-field-name' => $customField->column_name]]) !!}
        @endif
    </div>
@endforeach