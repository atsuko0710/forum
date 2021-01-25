<template>
    <div>
        <h1 v-text="user.name"></h1>

        <form v-if="canUpdate" method="POST" enctype="multipart/form-data">
            <!-- <input type="file" name="avatar" accept="image/*" @change="onChange"> -->
            <image-upload name="avatar" class="mr-1" @loaded="onLoad"></image-upload>
        </form>

        <img :src="avatar" width="200" height="200">
    </div>
</template>

<script>
    import ImageUpload from './ImageUpload.vue';

    export default {
        props: ['user'],

        components : { ImageUpload },

        data() {
            return {
                avatar:'/storage/'+this.user.avatar_path
            };
        },

        computed: {
            canUpdate() {
                // return this.authorize(user => user.id === this.user.id)
                return true;
            }
        },

        methods: {
            // onChange(e){
            //     if (! e.target.files.length) return;

            //     let avatar = e.target.files[0];

            //     let reader = new FileReader();

            //     reader.readAsDataURL(avatar);

            //     reader.onload = e => {
            //       this.avatar = e.target.result;
            //     };

            //     this.persist(avatar);
            // },

            onLoad(avatar){
                this.avatar = avatar.src;

                this.persist(avatar.file);
            },

            persist(avatar) {
                let data = new FormData();

                data.append('avatar',avatar);

                axios.post(`/api/users/${this.user.name}/avatar`,data)
                    .then(() => flash('Avatar uploaded!'));
            }
        }
    }
</script>