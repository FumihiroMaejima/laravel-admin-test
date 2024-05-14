@props([
    'class' => '',
    'name' => '',
    'isMultiple' => false,
    'isPreview' => false,
])
<div class="">
    <div id="{{$name . '_input-file-area'}}" class="upload-area d-flex justify-content-center">
        <div class="d-flex justify-content-center flex-column">
            <i class="fas fa-cloud-upload-alt fa-5x"></i>
            <p>Click OR Drag and drop a file</p>
        </div>
        <input
            type="file"
            id="{{$name . '_input-files'}}"
            name="{{$name}}"
            class="upload_file"
        >
    </div>
    <p id="{{$name . '_file-name'}}" class="upload_file_name"></p>
    @if ($isPreview)
        <div id="{{$name . '_preview-image'}}" class="preview-image"></>
    @endif
</div>

@section('css')
    <style>
        .upload-area {
            margin: auto;
            width: 85%;
            height: 200px;
            position: relative;
            border: 2px dotted rgba(0, 0, 0, .4);
        }
        .upload-area i {
            opacity: .1;
            width: 100%;
            text-align: center;
        }
        .upload-area p {
            width: 100%;
            opacity: .8;
        }

        .upload-area_dragover {
            background-color: rgba(0, 0, 0, .6);
            border: 4px dotted rgba(0, 0, 0, .4);
        }

        .upload-area_uploaded {
            display: none !important;
        }

        .upload_file {
            top: 0;
            left: 0;
            opacity: 0;
            position: absolute;
            width: 100%;
            height: 100%;

            &:hover {
                cursor: pointer;
            }
        }

        .upload_file_name {
            &:hover {
                cursor: pointer;
            }
        }

        .upload_file_name_reset-file-icon {
            color: #ff0000;
            padding: 0 8px;
            font-size: 16px;
            border: 1px solid #c6c6c6;
            border-radius: 10px;

            &:hover {
              cursor: pointer;
              border-color: #5f6674;
            }
          }

    </style>
@stop

@section('js')
    <script>
        console.log('Sample File Input');

        const fileInputAreaId = `{{$name}}_input-file-area`
        const fileInputId = `{{$name}}_input-files`
        const fileNameId = `{{$name}}_file-name`
        const fileResetId = `{{$name}}_file-reset`
        const previewImageId = `{{$name}}_preview-image`

        const isMultiple = !!`{{$isMultiple}}`
        const isPreview = !!`{{$isPreview}}`

        // const fileArea = document.getElementById('input-file-area');
        // const fileInput = document.getElementById('input-files');
        const fileArea = document.getElementById(fileInputAreaId);
        const fileInput = document.getElementById(fileInputId);
        const fileNameArea = document.getElementById(fileNameId);
        const preview = document.getElementById(previewImageId);

        let ImageData = null;

        /**
        * create reset button
        * @param {string} fileInputAreaId
        * @param {string} fileResetId
        * @param {string} fileNameId
        * @param {string} previewImageId
        * @param {HTMLElement} fileArea
        * @param {HTMLElement} fileInput
        * @param {HTMLElement} preview
        * @param {HTMLElement} fileNameArea
        * @param {boolean} isPreview
        * @return {void}
        */
        function setResetButton(fileInputAreaId, fileResetId, fileNameId, previewImageId, fileArea, fileInput, preview, fileNameArea, isPreview) {
            const tmpResetButton = document.createElement('span')
            tmpResetButton.textContent = 'x'
            tmpResetButton.classList.add('upload_file_name_reset-file-icon');
            tmpResetButton.setAttribute('id', fileResetId)

            fileNameArea.appendChild(tmpResetButton)

            tmpResetButton.addEventListener('click', function(evt){
                evt.preventDefault();
                console.log('click: ');

                fileInput.files = null
                fileInput.value = null
                fileNameArea.textContent = null

                if (isPreview) {
                    const tmpImg = document.getElementById('previewImageId')
                    preview.removeChild(tmpImg)
                }

                fileArea.classList.remove('upload-area_uploaded');
                tmpResetButton.remove()

            });
        }

        /**
        * set preview image
        * @param {File} file
        * @param {HTMLElement} fileArea
        * @param {HTMLElement} fileInput
        * @param {HTMLElement} fileNameArea
        * @param {HTMLElement} preview
        * @return {void}
        */
        function setImage(file, fileArea, fileInput, fileNameArea, preview) {
            // ファイルの読み込み
            const reader = new FileReader()
                reader.onload = () => {
                    ImageData = reader.result?.toString();
                    console.log('ImageData: ' + ImageData);

                    const tmpImg = document.createElement('img')
                    tmpImg.setAttribute('src', ImageData)
                    preview.appendChild(tmpImg)
            }
            reader.readAsDataURL(file);
        }


        fileInput.addEventListener('change', function(evt){
            console.log('change: ');

            const files = evt.dataTransfer.files;
            // ファイルをアップロードした時
            if (files) {
                let fileName = ''
                for(i = 0; i < files.length; i++) {
                    if (fileName !== '') {
                        fileName += ','
                    }
                    fileName += files[i].name
                }

                fileNameArea.textContent = fileName
                setResetButton(
                    fileInputAreaId,
                    fileResetId,
                    fileNameId,
                    previewImageId,
                    fileArea,
                    fileInput,
                    preview,
                    fileNameArea,
                    isPreview
                )

                fileArea.classList.add('upload-area_uploaded');
            }

            evt.preventDefault();
        });

        fileArea.addEventListener('dragover', function(evt){
            console.log('dragover: ');
            evt.preventDefault();
            fileArea.classList.add('upload-area_dragover');
        });

        fileArea.addEventListener('dragleave', function(evt){
            console.log('dragleave: ');
            evt.preventDefault();
            fileArea.classList.remove('upload-area_dragover');
        });
        fileArea.addEventListener('drop', function(evt){
            console.log('drop: ');
            evt.preventDefault();
            fileArea.classList.remove('upload-area_dragover');
            const files = evt.dataTransfer.files;
            if (!isMultiple && files.length > 1) {
                const errorMessage = 'drooped multi files'
                alert(errorMessage)
                throw new Error(errorMessage)
            }
            fileInput.files = files;

            // TODO 検証用
            console.log('files: ' + JSON.stringify(files, null, 2));
            console.log('files.length: ' + files.length);
            console.log('file: ' + JSON.stringify(files[0], null, 2));
            file = files[0];
            console.log('file.size: ' + file.size);
            console.log('file.type: ' + file.type);
            console.log('file.name: ' + file.name);

            let fileName = ''
            for(i = 0; i < files.length; i++) {
                if (fileName !== '') {
                    fileName += ','
                }
                fileName += files[i].name
            }

            fileNameArea.textContent = fileName
            setResetButton(
                fileInputAreaId,
                fileResetId,
                fileNameId,
                previewImageId,
                fileArea,
                fileInput,
                preview,
                fileNameArea,
                isPreview
            )

            if (!isMultiple && isPreview) {
                setImage(files[0], fileArea, fileInput, fileNameArea, preview)
                // ファイル名
                fileNameArea.textContent = file.name

                if (isPreview) {
                    // ファイルの読み込み
                    const reader = new FileReader()
                        reader.onload = () => {
                            ImageData = reader.result?.toString();
                            console.log('ImageData: ' + ImageData);
                    }
                    reader.readAsDataURL(file);
                }
            }
            fileArea.classList.add('upload-area_uploaded');
        });

    </script>
@stop
