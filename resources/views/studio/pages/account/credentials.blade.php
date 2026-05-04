@extends('studio.pages.account.main')
@section('title', __('Credential'))
@section('account_title', __('Credential'))
@section('account_desc', __('Manage your credential.'))
@push('css')
<link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/43.3.1/ckeditor5.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
a.cancel-action{
    background-color: #6c757d !important;
    border-color: #6c757d !important;

}
a.cancel-action:hover{
    background-color: #462abf !important;
    border-color: #462abf !important;
}

.mgl-custom {
    margin-top: 10px;
    text-align: center;
}
.value-and-action-container .form-control[name='phone']{
    width: 47%;
}

/* Discography links styles */
.discography-links-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.discography-link-item {
    margin-bottom: 5px;
}
.discography-link-item a {
    color: #462abf;
    text-decoration: none;
}
.discography-link-item a:hover {
    text-decoration: underline;
}
.discography-links-form {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 15px;
}
.discography-link-row {
    display: flex;
    gap: 10px;
    align-items: center;
    margin-bottom: 5px;
}
.discography-link-row input {
    flex: 1;
}
.add-more-link-btn {
    margin-top: 5px;
    align-self: flex-start;
}
.remove-link-btn {
    color: #dc3545;
    background: none;
    border: none;
    cursor: pointer;
    font-size: 16px;
    padding: 5px;
}
</style>
@php
$fullPhone = $user['full_phone'] ?? '';
$phone = $user['phone'] ?? '';

$formattedPhone = preg_replace('/(\d{3})(\d{3})(\d{3})/', '$1 $2 $3', $fullPhone);
@endphp
@section('account_content')
    <p class="title-profile">{{__('YOUR INFORMATION')}}</p>
    <div class="manage-credential-items" data-url="{{ rrt_route($controllerName . '/postCredentials') }}"
        data-id="{{ $user['id'] ?? '' }}">
        <div class="credential-item">
            <p class="item-label"> E-mail </p>
            <div class="value-and-action-container">
                <span class="item-value" name = "email">{{ $user['email'] ?? '' }}</span>
                <a class="item-action" data-name="email" href="#">{{__('Change e-mail address')}}</a>
            </div>
        </div>
        <div class="credential-item">
            <p class="item-label">{{__('Username')}} </p>
            <div class="value-and-action-container">
                <span class="item-value" name = "username">{{ $user['username'] ?? '' }}</span>
                <a class="item-action" data-name="username" href="#">{{__('Change username')}}</a>
            </div>
        </div>
        <div class="credential-item">
            <p class="item-label">{{__('Password')}} </p>
            <div class="value-and-action-container">
                <span class="item-value" name = "password">********</span>
                <a class="item-action" data-name="password" href="#">{{__('Change password')}}</a>
            </div>
        </div>
        <div class="credential-item">
            <p class="item-label">{{__('Phone Number')}} </p>
            <div class="value-and-action-container">


                <span class="item-value" name="phone">{{ $fullPhone }}</span>
                <a class="item-action" data-name = "phone" href="#" data-phone="{{$phone??''}}">{{__('Add phone number')}}</a>
            </div>
        </div>
{{--        <div class="credential-item">--}}
{{--            <p class="item-label">{{__('Accomplishments')}} </p>--}}
{{--            <div class="value-and-action-container">--}}
{{--                <span class="item-value" name = "accomplishments">{!! $user['accomplishments'] ?? '' !!}</span>--}}
{{--                <a class="item-action" data-name = "accomplishments" href="#">{{__('Add Accomplishments')}}</a>--}}
{{--            </div>--}}
{{--        </div>--}}
        <div class="credential-item">
            <p class="item-label">{{__('Discography')}} </p>
            <div class="value-and-action-container">
                <div class="discography-links-container">
                    @php
                        // Check if discography is stored as JSON or comma-separated text
                        $discographyLinks = [];
                        if (!empty($user['discography'])) {
                            // Try to parse as JSON first
                            try {
                                $jsonLinks = json_decode($user['discography'], true);
                                if (is_array($jsonLinks)) {
                                    $discographyLinks = $jsonLinks;
                                } else {
                                    // If not valid JSON, treat as comma-separated text
                                    $textLinks = explode(', ', $user['discography']);
                                    foreach ($textLinks as $link) {
                                        // Check if link contains a title and URL
                                        $parts = explode(' - ', $link, 2);
                                        if (count($parts) == 2) {
                                            $discographyLinks[] = [
                                                'title' => $parts[0],
                                                'url' => $parts[1]
                                            ];
                                        }
                                    }
                                }
                            } catch (\Exception $e) {
                                // If JSON parsing fails, treat as comma-separated text
                                $textLinks = explode(', ', $user['discography']);
                                foreach ($textLinks as $link) {
                                    // Check if link contains a title and URL
                                    $parts = explode(' - ', $link, 2);
                                    if (count($parts) == 2) {
                                        $discographyLinks[] = [
                                            'title' => $parts[0],
                                            'url' => $parts[1]
                                        ];
                                    }
                                }
                            }
                        }
                    @endphp
                    @if(empty($discographyLinks))
                        <span class="item-value" name="discography">{{__('No links added')}}</span>
                    @else
                        <div class="discography-links-list">
                            @foreach($discographyLinks as $index => $link)
                                <div class="discography-link-item">
                                    <a href="{{ $link['url'] }}" target="_blank">{{ $link['title'] }}</a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                <a class="item-action" data-name="discography" href="#">{{__('Manage Discography Links')}}</a>
            </div>
        </div>
        {{-- <div class="credential-item">
            <p class="item-label">{{__('Youtube Link')}} </p>
            <div class="value-and-action-container">
                <span class="item-value" name="youtube_link">{!! $user['youtube_link'] ?? '' !!}</span>
                <a class="item-action" data-name = "youtube_link" href="#">{{__('Add Youtube Link')}}</a>
            </div>
        </div>
        <div class="credential-item">
            <p class="item-label">{{__('Work History')}} </p>
            <div class="value-and-action-container">
                <span class="item-value" name = "work_history">{!! $user['work_history'] ?? '' !!}</span>
                <a class="item-action" data-name = "work_history" href="#">{{__('Add Work History')}}</a>
            </div>
        </div> --}}
    </div>
@endsection
@push('script')
    <!-- Import Ck editor -->
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <!-- End import -->
    <script>
        countries = @json($countries);
        user_country = '{{$user->country_code}}'
        let editorInstance;
        let itemWrap = $(".manage-credential-items");
        const itemAction = $(".item-action");
        function createCKEditorForTextarea(textareaElement) {
            if (!textareaElement || textareaElement.tagName !== 'TEXTAREA') {
                console.error('Invalid element for CKEditor initialization');
                return;
            }
            const idValue = textareaElement.id;
            ClassicEditor
                .create(textareaElement)
                .then(editor => {
                    editorInstance = editor;
                    editor.model.document.on('change:data', () => {
                        document.getElementById(idValue).value = editor.getData();
                    });
                })
                .catch(error => {
                    console.error(error);
                });
        }
        function showForm(ele){
            let id = itemWrap.data('id');
            const name = ele.data('name');
            const eleName = $(`.item-value[name=${name}]`);
            let currentValue = eleName.text();
            let inputType = 'text';
            let phoneData;
            
            if(name == 'password') {
                currentValue = '';
                inputType = 'password';
            }
            
            if(name == "phone") {
                countryCode = generateCountryCodeDropdown();
                ele.closest('.value-and-action-container').prepend(countryCode);
                phoneData = ele.data('phone')
                console.log(phoneData)
            }
            
            let inputElement;
            
            // Handle username and email fields with standard input elements
            if(name == 'username' || name == 'email') {
                inputElement = $("<input data-value-old='"+currentValue+"' type='"+inputType+"' class='form-control' value='" + currentValue + "' id='" + name + "' name='" + name + "'/>");
            }
            else if(name == 'phone') {
                inputElement = $("<input data-full-phone='"+currentValue+"' data-value-old='"+phoneData+"' type='"+inputType+"' class='form-control' value='" + phoneData + "' id='" + name + "' name='" + name + "'/>");
            }
            else if(name == 'work_history' || name == 'accomplishments' || name=='youtube_link') {
                inputElement = $("<textarea data-value-old='"+currentValue+"' class='form-control'  id='" + name + "' name='" + name + "'>"+currentValue+"</textarea>");
            }
            // Handle discography with multiple links
            else if(name == 'discography') {
                // Luôn hiển thị form khi click vào action button
                inputElement = $("<div class='discography-links-form' id='discography-form'></div>");
                
                // Try to parse existing links
                let discographyLinks = [];
                
                // Get saved links data from container
                const container = $('.discography-links-container');
                const jsonData = container.attr('data-json') || '';
                const textData = container.attr('data-links') || '';
                
                console.log("Found data for form:", { json: jsonData, text: textData });
                
                // Ưu tiên lấy dữ liệu từ JSON trước
                if (jsonData && jsonData !== '[]') {
                    try {
                        discographyLinks = JSON.parse(jsonData);
                    } catch (e) {
                        console.error("Error parsing JSON data:", e);
                    }
                }
                
                // Nếu không có JSON hoặc parse lỗi, thử dùng text
                if (discographyLinks.length === 0 && textData) {
                    const textLinks = textData.split(', ');
                    textLinks.forEach(link => {
                        const parts = link.split(' - ', 2);
                        if (parts.length === 2) {
                            discographyLinks.push({
                                title: parts[0],
                                url: parts[1]
                            });
                        }
                    });
                }
                
                // Nếu vẫn không có dữ liệu, thử lấy từ DOM
                if (discographyLinks.length === 0) {
                    $('.discography-link-item a').each(function() {
                        discographyLinks.push({
                            title: $(this).text(),
                            url: $(this).attr('href')
                        });
                    });
                }
                
                // Nếu không có dữ liệu, tạo một row trống
                if (discographyLinks.length === 0) {
                    discographyLinks = [{title: '', url: ''}];
                }
                
                console.log("Discography links for form:", discographyLinks);
                
                // Create input fields for each link
                discographyLinks.forEach((link, index) => {
                    const linkRow = $(`
                        <div class="discography-link-row" data-index="${index}">
                            <input type="text" class="form-control link-title" placeholder="Link Title" value="${link.title || ''}" />
                            <input type="text" class="form-control link-url" placeholder="Link URL" value="${link.url || ''}" />
                            ${index > 0 ? '<button type="button" class="remove-link-btn"><i class="fas fa-trash"></i></button>' : ''}
                        </div>
                    `);
                    inputElement.append(linkRow);
                });
                
                // Add button to add more links
                const addBtn = $(`
                    <button type="button" class="btn btn-sm btn-outline-primary add-more-link-btn">
                        <i class="fas fa-plus"></i> Add Another Link
                    </button>
                `);
                inputElement.append(addBtn);
                
                // Add hidden input to store the final JSON
                inputElement.append(`<input type="hidden" id="discography" name="discography" />`);
            }
            // For any other field types, default to standard input
            else {
                inputElement = $("<input data-value-old='"+currentValue+"' type='"+inputType+"' class='form-control' value='" + currentValue + "' id='" + name + "' name='" + name + "'/>");
            }
            
            let buttons = `
            <div class="mgl-custom">
            <a class="cancel-action btn btn-primary ladda-button" data-name = "${name}" href="#">{{__('Cancel')}}</a>
            <a class="save-action btn btn-primary ladda-button" data-name = "${name}" href="#"><span class="ladda-label">{{__('Save Changes')}}</span></a>
            </div>`;
            
            ele.closest('.credential-item').append(buttons);
            
            if (name == 'discography') {
                $('.discography-links-container').replaceWith(inputElement);
            } else {
                eleName.replaceWith(inputElement);
            }
            
            if (inputElement.is('textarea') && (inputElement.attr('id') === 'work_history' || inputElement.attr('id') === 'accomplishments' || inputElement.attr('id') === 'youtube_link')) {
                createCKEditorForTextarea(inputElement[0]);
            }
            
            // Attach event handlers after DOM is updated
            setTimeout(() => {
                // Add more links
                $('.add-more-link-btn').on('click', function() {
                    const newIndex = $('.discography-link-row').length;
                    const newRow = $(`
                        <div class="discography-link-row" data-index="${newIndex}">
                            <input type="text" class="form-control link-title" placeholder="Link Title" />
                            <input type="text" class="form-control link-url" placeholder="Link URL" />
                            <button type="button" class="remove-link-btn"><i class="fas fa-trash"></i></button>
                        </div>
                    `);
                    $(this).before(newRow);
                    attachRemoveHandler();
                });
                
                // Function to attach remove handlers
                function attachRemoveHandler() {
                    $('.remove-link-btn').off('click').on('click', function() {
                        $(this).closest('.discography-link-row').remove();
                        updateDiscographyInput();
                    });
                }
                
                // Update hidden input when links change
                function updateDiscographyInput() {
                    const links = [];
                    let textLinks = [];
                    
                    $('.discography-link-row').each(function() {
                        const title = $(this).find('.link-title').val().trim();
                        const url = $(this).find('.link-url').val().trim();
                        if (title && url) {
                            links.push({ title, url });
                            textLinks.push(`${title} - ${url}`);
                        }
                    });
                    
                    // Lưu dạng JSON vào data attribute để sử dụng sau này
                    window.discographyLinksJSON = JSON.stringify(links);
                    
                    // Chuyển thành text để post lên server
                    const textValue = textLinks.length > 0 ? textLinks.join(', ') : '';
                    $('#discography').val(textValue);
                    
                    // Log để check
                    console.log("Discography data prepared:", {
                        id: itemWrap.data('id'),
                        name: 'discography',
                        value: textValue,
                        jsonData: window.discographyLinksJSON
                    });
                }
                
                // Attach change event to all inputs
                $(document).on('input', '.link-title, .link-url', updateDiscographyInput);
                
                // Initial attach of remove handlers
                attachRemoveHandler();
                
                // Initial value update
                updateDiscographyInput();
            }, 100);
        }
        function uploadData(ele, elementTag){
            let id = itemWrap.data('id');
            let url = itemWrap.data('url');
            const name = ele.attr('name');
            let value = ele.val();
            let data = {
                id,
                name,
                value
            }
            
            if(name == 'phone'){
                data.country_code = $('#country_code_id_input').val()
            }
            
            if(!value) {
                return showNotify('error','Error',`Please enter your ${name}`);
            }
            
            // Validate unique fields (username, email, phone) before submitting
            if(name === 'username' || name === 'email' || name === 'phone') {
                // Show loading indicator
                let laddaBtn = Ladda.create(elementTag[0]);
                laddaBtn.start();
                
                // Check if value is the same as the original (no change)
                const originalValue = ele.data('value-old');
                if(name === 'phone') {
                    // For phone, check the plain number without formatting
                    if(value === originalValue) {
                        laddaBtn.stop();
                        if(name === 'password') {
                            value = '********';
                        }
                        if(name === 'phone'){
                            let contrycode = data.country_code
                            ele.replaceWith("<span class='item-value' name = '"+name+"'>" +'+('+contrycode+ ') '+ value +"</span>");
                        } else {
                            ele.replaceWith("<span class='item-value' name = '"+name+"'>" +value +"</span>");
                        }
                        elementTag.closest('.mgl-custom').remove();
                        if(name === "phone"){
                            user_country = $('#country_code_id_input').val();
                            $('#country_code_id_input').remove();
                        }
                        return;
                    }
                } else if(value === originalValue) {
                    laddaBtn.stop();
                    ele.replaceWith("<span class='item-value' name = '"+name+"'>" +value +"</span>");
                    elementTag.closest('.mgl-custom').remove();
                    return;
                }
                
                // Check if the value is unique via AJAX
                $.ajax({
                    type: "post",
                    url: url,
                    data: {
                        id: id,
                        name: 'check_' + name,
                        value: value
                    },
                    dataType: "json",
                    success: function(response) {
                        laddaBtn.stop();
                        if(response.error === 1) {
                            showNotify('error', '', response.msg);
                        } else {
                            // If unique, proceed with the actual update
                            submitUpdateData(data, ele, elementTag, name, value);
                        }
                    },
                    error: function() {
                        laddaBtn.stop();
                        showNotify('error', '', 'Error checking uniqueness. Please try again.');
                    }
                });
            } else {
                // For non-unique fields, submit directly
                submitUpdateData(data, ele, elementTag, name, value);
            }
        }
        
        function submitUpdateData(data, ele, elementTag, name, value) {
            $.ajax({
                type: "post",
                url: itemWrap.data('url'),
                data: data,
                dataType: "json",
                success: function(response) {
                    const error = response.error;
                    const msg = response.msg.charAt(0).toUpperCase() + response.msg.slice(1);
                    const type = error == 1 ? "error" : "success";
                    showNotify(type, '', msg)
                    if(error == 0) {
                        if(name == 'password') {
                            value = '********';
                        }
                        if(name == 'phone'){
                            let contrycode = data.country_code
                            ele.replaceWith("<span class='item-value' name = '"+name+"'>" +'+('+contrycode+ ') '+ value +"</span>");
                        }
                        else if(name == 'discography') {
                            // Parse the saved links
                            let linksHtml = '';
                            
                            // First try to create HTML from saved JSON
                            try {
                                const links = JSON.parse(window.discographyLinksJSON || '[]');
                                if (links && links.length > 0) {
                                    linksHtml = '<div class="discography-links-list">';
                                    links.forEach(link => {
                                        if (link.title && link.url) {
                                            linksHtml += `
                                                <div class="discography-link-item">
                                                    <a href="${link.url}" target="_blank">${link.title}</a>
                                                </div>`;
                                        }
                                    });
                                    linksHtml += '</div>';
                                } else {
                                    linksHtml = '<span class="item-value" name="discography">{{__("No links added")}}</span>';
                                }
                            } catch (e) {
                                // If JSON parsing fails, fallback to text format
                                const textLinks = value.split(', ');
                                if (textLinks.length > 0 && textLinks[0] !== '') {
                                    linksHtml = '<div class="discography-links-list">';
                                    textLinks.forEach(link => {
                                        const parts = link.split(' - ', 2);
                                        if (parts.length === 2) {
                                            const title = parts[0];
                                            const url = parts[1];
                                            linksHtml += `
                                                <div class="discography-link-item">
                                                    <a href="${url}" target="_blank">${title}</a>
                                                </div>`;
                                        }
                                    });
                                    linksHtml += '</div>';
                                } else {
                                    linksHtml = '<span class="item-value" name="discography">{{__("No links added")}}</span>';
                                }
                            }
                            
                            // Replace the form with the links list - store both formats
                            const linksContainer = $(`<div class="discography-links-container">${linksHtml}</div>`);
                            // Lưu cả text và JSON format để có thể sử dụng sau này
                            linksContainer.attr('data-links', value);
                            linksContainer.attr('data-json', window.discographyLinksJSON || '[]');
                            $('#discography-form').replaceWith(linksContainer);
                            elementTag.closest('.mgl-custom').remove();
                        } 
                        else {
                            ele.replaceWith("<span class='item-value' name = '"+name+"'>" +value +"</span>");
                        }
                        elementTag.closest('.mgl-custom').remove();
                        if(name == "phone"){
                            user_country = $('#country_code_id_input').val();
                            $('#country_code_id_input').remove();
                        }
                    }
                }
            });
        }

        function uploadEditor(ele,elementTag){
            let id = itemWrap.data('id');
            let url = itemWrap.data('url');
            let textareaElement = ele.find('textarea');
            let editorElement = ele.find('.ck-editor');
            let name = textareaElement.attr('name');
            let value = textareaElement.val();

            let data = {
                id,
                name,
                value
            }

            $.ajax({
                type: "post",
                url: url,
                data: data,
                dataType: "json",
                success: function(response) {
                    const error = response.error;
                    const msg = response.msg.charAt(0).toUpperCase() + response.msg.slice(1);
                    const type = error == 1 ? "error" : "success";
                    showNotify(type, '', msg)
                    if(error == 0) {
                        editorElement.remove();
                        textareaElement.replaceWith("<span class='item-value' name='" + name + "'>" +value +"</span>");
                        elementTag.closest('.mgl-custom').remove();
                    }
                }
            });
        }
        itemAction.click(function() {
            // Xóa form hiện tại nếu có
            $('.mgl-custom').remove();
            $('#discography-form').remove();
            
            // Đảm bảo chỉ có một form được hiển thị tại một thời điểm
            $('.form-control, .discography-links-form').each(function() {
                let name = $(this).attr('name');
                if (name) {
                    let valueElement = $("<span class='item-value' name='" + name + "'>" + $(this).data('value-old') + "</span>");
                    $(this).replaceWith(valueElement);
                }
            });
            
            // Hiển thị form mới
            showForm($(this));
        });

        $(document).on('click', '.save-action', function() {
            let closestContainer = $(this).closest('.credential-item').find(".value-and-action-container");
            let inputElement = closestContainer.find('input.form-control');
            let spanElement = closestContainer.find('span.item-value');
            let textareaElement = closestContainer.find('textarea.form-control');
            let elementTag = $(this);
            let name = $(this).data('name');
            
            // Xử lý riêng cho discography
            if (name === 'discography') {
                let discographyInput = $('#discography');
                if (discographyInput.length > 0) {
                    let value = discographyInput.val();
                    let jsonData = window.discographyLinksJSON || '[]';
                    
                    let data = {
                        id: itemWrap.data('id'),
                        name: 'discography',
                        value: value
                    };
                    
                    // Log dữ liệu trước khi gửi
                    console.log("Sending discography data:", data);
                    
                    $.ajax({
                        type: "post",
                        url: itemWrap.data('url'),
                        data: data,
                        dataType: "json",
                        success: function(response) {
                            const error = response.error;
                            const msg = response.msg.charAt(0).toUpperCase() + response.msg.slice(1);
                            const type = error == 1 ? "error" : "success";
                            showNotify(type, '', msg)
                            
                            if(error == 0) {
                                // Parse the saved links
                                let linksHtml = '';
                                
                                // First try to create HTML from saved JSON
                                try {
                                    const links = JSON.parse(jsonData);
                                    if (links && links.length > 0) {
                                        linksHtml = '<div class="discography-links-list">';
                                        links.forEach(link => {
                                            if (link.title && link.url) {
                                                linksHtml += `
                                                    <div class="discography-link-item">
                                                        <a href="${link.url}" target="_blank">${link.title}</a>
                                                    </div>`;
                                            }
                                        });
                                        linksHtml += '</div>';
                                    } else {
                                        linksHtml = '<span class="item-value" name="discography">{{__("No links added")}}</span>';
                                    }
                                } catch (e) {
                                    // If JSON parsing fails, fallback to text format
                                    const textLinks = value.split(', ');
                                    if (textLinks.length > 0 && textLinks[0] !== '') {
                                        linksHtml = '<div class="discography-links-list">';
                                        textLinks.forEach(link => {
                                            const parts = link.split(' - ', 2);
                                            if (parts.length === 2) {
                                                const title = parts[0];
                                                const url = parts[1];
                                                linksHtml += `
                                                    <div class="discography-link-item">
                                                        <a href="${url}" target="_blank">${title}</a>
                                                    </div>`;
                                            }
                                        });
                                        linksHtml += '</div>';
                                    } else {
                                        linksHtml = '<span class="item-value" name="discography">{{__("No links added")}}</span>';
                                    }
                                }
                                
                                // Replace the form with the links list - store both formats
                                const linksContainer = $(`<div class="discography-links-container">${linksHtml}</div>`);
                                // Lưu cả text và JSON format để có thể sử dụng sau này
                                linksContainer.attr('data-links', value);
                                linksContainer.attr('data-json', jsonData);
                                $('#discography-form').replaceWith(linksContainer);
                                elementTag.closest('.mgl-custom').remove();
                            }
                        }
                    });
                    return;
                }
            }
            
            // submit form cho các trường hợp khác
            if (inputElement.length > 0) {
                uploadData(inputElement, elementTag);
            }
            if (textareaElement.length > 0) {
                uploadEditor(closestContainer, elementTag);
            }
        });

        $(document).on('click', '.cancel-action', function() {
            let closestContainer = $(this).closest('.credential-item').find(".value-and-action-container");
            let inputElement = closestContainer.find('input.form-control');
            let spanElement = closestContainer.find('span.item-value');
            let textareaElement = closestContainer.find('textarea.form-control');
            let discographyForm = closestContainer.find('#discography-form');
            $(this).closest('.mgl-custom').remove();
            // submit form
            if (inputElement.length > 0) {
                cancelbuttonfunction(inputElement);
            }
            if (textareaElement.length > 0) {
                cancelbuttonTexteditorfunction(closestContainer);
            }
            if (discographyForm.length > 0) {
                cancelDiscographyForm(discographyForm);
            }
        });
        
        function cancelDiscographyForm(ele) {
            // Get the data from the hidden data attribute if available
            let discographyData = '';
            
            // Cố gắng lấy data từ hidden input trước nếu form đang được hiển thị
            const hiddenInput = $('#discography');
            if (hiddenInput.length > 0 && hiddenInput.val()) {
                discographyData = hiddenInput.val();
            } else {
                // Nếu không có hidden input (hoặc không có dữ liệu), thử lấy từ container
                const container = $('.discography-links-container');
                if (container.length > 0) {
                    discographyData = container.data('links') || '';
                }
            }
            
            console.log("Discography data for cancel:", discographyData);
            let linksHtml = '';
            
            // First try to handle as JSON data
            try {
                if (discographyData) {
                    // Try to parse as JSON first
                    try {
                        const links = JSON.parse(discographyData);
                        if (links && links.length > 0) {
                            linksHtml = '<div class="discography-links-list">';
                            links.forEach(link => {
                                if (link.title && link.url) {
                                    linksHtml += `
                                        <div class="discography-link-item">
                                            <a href="${link.url}" target="_blank">${link.title}</a>
                                        </div>`;
                                }
                            });
                            linksHtml += '</div>';
                        } else {
                            linksHtml = '<span class="item-value" name="discography">{{__("No links added")}}</span>';
                        }
                    } catch (e) {
                        // If JSON parsing fails, treat as comma-separated text
                        const textLinks = discographyData.split(', ');
                        if (textLinks.length > 0 && textLinks[0] !== '') {
                            linksHtml = '<div class="discography-links-list">';
                            textLinks.forEach(link => {
                                const parts = link.split(' - ', 2);
                                if (parts.length === 2) {
                                    const title = parts[0];
                                    const url = parts[1];
                                    linksHtml += `
                                        <div class="discography-link-item">
                                            <a href="${url}" target="_blank">${title}</a>
                                        </div>`;
                                }
                            });
                            linksHtml += '</div>';
                        } else {
                            linksHtml = '<span class="item-value" name="discography">{{__("No links added")}}</span>';
                        }
                    }
                } else {
                    // If no data available, use empty state
                    linksHtml = '<span class="item-value" name="discography">{{__("No links added")}}</span>';
                }
            } catch (e) {
                console.error("Error handling discography data:", e);
                linksHtml = '<span class="item-value" name="discography">{{__("No links added")}}</span>';
            }
            
            // Replace the form with the links container
            const linksContainer = $(`<div class="discography-links-container">${linksHtml}</div>`);
            if (discographyData) {
                linksContainer.attr('data-links', discographyData);
            }
            ele.replaceWith(linksContainer);
        }
        
        function cancelbuttonfunction(ele){
            console.log(ele)
            const name = ele.attr('name');
            let value = ele.data('value-old');
            
            if(name == 'password') {
                value = '********';
            }
            else if(name == 'phone'){
                value = ele.data('full-phone');
            }
            
            ele.replaceWith("<span class='item-value' name = '"+name+"'>" + value +"</span>");

            if(name == "phone"){
                $('#country_code_id_input').remove();
            }
        }
        function cancelbuttonTexteditorfunction(ele){
            let textareaElement = ele.find('textarea');
            let editorElement = ele.find('.ck-editor');
            let name = textareaElement.attr('name');
            let value = textareaElement.data('value-old');;
            editorElement.remove();
            textareaElement.replaceWith("<span class='item-value' name='" + name + "'>" + value +"</span>");
        }

        window.onload = function() {
            if ( window.location.protocol === "file:" ) {
                alert( "This sample requires an HTTP server. Please serve this file with a web server." );
            }
        };

        $(document).on('change', "#country_code_id_input", function(){
            let phone_example = $(this).find("option:selected").data('phone-example');
            $('[name="phone"]').prop("placeholder", phone_example ? phone_example : "000 000 000");
            $('[name="phone"]').val('');
        })
        function formatPhone(input) {
            console.log("input: ",input)
            const regex = /^[a-zA-Z0-9]*$/;

            if(input){
                if (!regex.test(input)) {
                    input = input.replace(/[^a-zA-Z0-9]/g, '');
                }
                return input.replace(/(\d{3})(\d{3})(\d{3})/, '$1 $2 $3');
            }
            return input;
        }
       formatPhone();
        $(document).on('keyup', '#phone', function() {
            // Remove non-digit characters
            $("#phone").val(formatPhone($('[name="phone"]').val()));
        });

        function generateCountryCodeDropdown() {
        const selectElement = document.createElement('select');
        selectElement.type = 'text';
        selectElement.name = 'country_code';
        selectElement.id = 'country_code_id_input';
        selectElement.style.height = '36px';
        selectElement.style.width = '80px';
        selectElement.classList.add('form-control');
        selectElement.setAttribute('placeholder', 'Enter Your Country Code');

        // Create the default "Please Choose Country" option
        // const defaultOption = document.createElement('option');
        // defaultOption.value = '';
        // defaultOption.textContent = 'Please Choose Country';
        // selectElement.appendChild(defaultOption);

        // Loop through the countries array to create the country options
        countries.forEach(country => {
            const option = document.createElement('option');
            option.value = country.phone_code;
            option.dataset.phoneExample = country.phone_example;
            option.textContent = `(+${country.phone_code}) ${country.name} `;

            // Check if the country phone code matches the user's country code
            if (country.phone_code == user_country) {
                option.selected = true;
            }

            selectElement.appendChild(option);
        });

    return selectElement;
}
    </script>
@endpush
