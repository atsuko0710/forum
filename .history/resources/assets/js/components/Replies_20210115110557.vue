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
                dataSet: false,
                items:this.data,
                endp
            }
        },

        created() {
            this.fetch();
        },

        methods: {
            remove(index) {
                this.items.splice(index, 1);
                this.$emit('remove')
                console.log(repliesCount)
                flash("成功删除回复！");
            },

            fetch() {
                axios.get(this.url()).then(this.refresh);
            },

            url() {
                return `${location.pathname}/replies`;
            },

            refresh(response) {
                console.log(response);
            }
        },
    }
</script>