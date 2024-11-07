<template>
    <Editor api-key="bvu9lokg76dk4mx0sipczxrdbcmps5ll9px9olsfpqjywybo" v-model="content" :init="{
        menubar: false,
        plugins: 'lists link image emoticons',
        toolbar: 'styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | link image emoticons | undo redo' ,
    }" />
</template>

<script>
export default {
    // Mendefinisikan props
    props: {
        modelValue: {
            type: String,
            default: ""
        }
    },
    components: {
        Editor
    },
    // Komponen Editor akan menggunakan data lokal 'content' yang terikat dengan v-model
    data() {
        return {
            content: this.modelValue // Menggunakan nilai awal dari props
        }
    },
    watch: {
        // Melakukan sinkronisasi antara model dan v-model
        content(newValue) {
            this.$emit('update:modelValue', newValue); // Mengirimkan event untuk update v-model
        }
    }
}
//import Editor
import Editor from '@tinymce/tinymce-vue';
</script>
