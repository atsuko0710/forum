<script>
    import Favorite from './Favorite.vue';

    export default {
        props: ['attributes'],

        components: {Favorite},

        data() {
            return {
              editing: false,
              body: this.attributes.body
            };
        },

        methods: {
            update() {
                axios.patch('replies/' + this.attributes.id, {
                    body:this.body
                });

                this.editing = false;

                flash("更新成功！");
            },

            destroy() {
                axios.delete('replies/' + this.attributes.id);

                $(this.$el).fadeOut(300, () => {
                    flash("删除回复成功！");
                });
            }
        },
    }
</script>