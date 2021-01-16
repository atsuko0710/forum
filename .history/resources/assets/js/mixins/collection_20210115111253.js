export default {
    data() {
        return {
            items : []
        }
    },

    methods: {
        add(item) {
            this.item.push(item);
            this.$emit('added');
        },

        remove(index) {
            this.item.splice(index, 1);
            this.$emit('remove')
        }
    },
}