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
        }
    },
}