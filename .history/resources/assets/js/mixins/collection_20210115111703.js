export default {
    data() {
        return {
            items : []
        }
    },

    methods: {
        add(item) {
            this.items.push(item);
            this.$emit('added');
        },

        remove(index) {
            this.item.splice(index, 1);
            this.$emit('removed');
            flash('回复已经删除');
        }
    },
}