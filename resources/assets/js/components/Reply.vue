<script>
    export default {
        props: ['attributes'],

        data() {
            return {
              editing: false,
              body: this.attributes.body
            };
        }
    }
</script>