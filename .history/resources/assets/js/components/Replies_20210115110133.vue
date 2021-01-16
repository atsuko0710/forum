<template>
    <div>
        <div v-for="(reply, index) in items" :key="reply.id">
            <reply :reply="reply" @deleted="remove(index)"></reply>
        </div>
    </div>
</template>

<script>
    import Reply from './Reply.vue';

    export default {
        props: ['data'],

        components: { Reply },

        data() {
            return {
                items:this.data
            }
        },

        methods: {
            remove(index) {
                this.items.splice(index, 1);
                this.$emit('remove')
                console.log(repliesCount)
                flash("成功删除回复！");
            },

            fetch() {
                axios.get()
            },

            refresh(response) {
                console.log(response);
            }
        },
    }
</script>